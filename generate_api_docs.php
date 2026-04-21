<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$routes = app('router')->getRoutes();
$markdown = "# API Routes Detailed Documentation\n\n";

$groupedRoutes = [];
foreach ($routes as $route) {
    if (!str_starts_with($route->uri(), 'api/')) continue;
    $uri = str_replace('api/', '', $route->uri());
    $prefix = explode('/', $uri)[0];
    $groupedRoutes[$prefix][] = $route;
}

foreach ($groupedRoutes as $prefix => $group) {
    if (!$prefix) continue;
    $title = ucfirst($prefix);
    $markdown .= "## {$title}\n\n";
    
    foreach ($group as $route) {
        $methods = array_diff($route->methods(), ['HEAD']);
        $method = implode('|', $methods);
        $uri = str_replace('api/', '', $route->uri());
        $action = $route->getActionName();
        $name = $route->getName();
        
        $markdown .= "### `{$method}` /{$uri}\n";
        $markdown .= "- **Action:** `{$action}`\n";
        if ($name) $markdown .= "- **Name:** `{$name}`\n";
        
        // Path parameters
        preg_match_all('/\{.*?\}/', $uri, $pathParams);
        $markdown .= "- **Path Parameters:** " . (!empty($pathParams[0]) ? implode(', ', $pathParams[0]) : "None") . "\n";
        
        // Determine body
        $bodyParams = [];
        $returns = 'JSON Response';
        if (str_contains($action, '@')) {
            try {
                list($class, $methodName) = explode('@', $action);
                if (class_exists($class)) {
                    $reflector = new ReflectionMethod($class, $methodName);
                    $file = $reflector->getFileName();
                    $start = $reflector->getStartLine();
                    $end = $reflector->getEndLine();
                    $lines = file($file);
                    $methodCode = implode("", array_slice($lines, $start - 1, $end - $start + 1));
                    
                    // Look for $request->validate([ 'field' => ... ])
                    if (preg_match('/validate\(\s*\[(.*?)\]\s*\)/s', $methodCode, $matches)) {
                        if (preg_match_all("/['\"](.*?)['\"]\s*=>/", $matches[1], $ruleMatches)) {
                            $bodyParams = array_unique($ruleMatches[1]);
                        }
                    }
                    
                    // Look for specific FormRequest mapping
                    $parameters = $reflector->getParameters();
                    foreach ($parameters as $param) {
                        $type = $param->getType();
                        if ($type && !$type->isBuiltin()) {
                            $typeName = $type->getName();
                            if (is_subclass_of($typeName, 'Illuminate\Foundation\Http\FormRequest')) {
                                $reqReflector = new ReflectionClass($typeName);
                                if ($reqReflector->hasMethod('rules')) {
                                    $rulesMethod = $reqReflector->getMethod('rules');
                                    $reqFile = $reqReflector->getFileName();
                                    $rStart = $rulesMethod->getStartLine();
                                    $rEnd = $rulesMethod->getEndLine();
                                    $rLines = file($reqFile);
                                    $rCode = implode("", array_slice($rLines, $rStart - 1, $rEnd - $rStart + 1));
                                    if (preg_match_all("/['\"](.*?)['\"]\s*=>/", $rCode, $rMatches)) {
                                        $bodyParams = array_merge($bodyParams, $rMatches[1]);
                                    }
                                }
                            }
                        }
                    }
                    
                    // Look for response()->json(...)
                    if (str_contains($methodCode, 'response()->json')) {
                         $returns = 'JSON Object / Array';
                    } elseif (str_contains($methodCode, 'return $')) {
                         $returns = 'Data Payload (JSON implied by Laravel API wrapper)';
                    }
                    
                    // Look for $request->all() or $request->input or $request->field
                    if (empty($bodyParams) && (in_array('POST', $methods) || in_array('PUT', $methods))) {
                        if (preg_match_all('/\$request->([a-zA-Z0-9_]+)/', $methodCode, $reqMatches)) {
                           $temp = array_diff($reqMatches[1], ['all', 'validate', 'file', 'bearerToken', 'user', 'input', 'header']);
                           if (!empty($temp)) $bodyParams = array_unique($temp);
                        }
                    }
                }
            } catch (\Exception $e) { }
        }
        
        if (in_array('POST', $methods) || in_array('PUT', $methods) || in_array('PATCH', $methods)) {
            if (!empty($bodyParams)) {
                $markdown .= "- **Body Parameters:**\n";
                foreach ($bodyParams as $bp) {
                    $markdown .= "  - `{$bp}`\n";
                }
            } else {
                $markdown .= "- **Body Parameters:**  Variable / Undefined (Checks controller logic)\n";
            }
        } else {
            $markdown .= "- **Body Parameters:** None\n";
        }
        
        // Return
        $markdown .= "- **Returns:** {$returns}\n\n";
    }
}

file_put_contents('api_routes_summary.md', $markdown);
echo "Generated detailed docs!";
