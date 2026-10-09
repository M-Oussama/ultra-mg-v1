# Certify ZIP uploads on production

The observed failed request contained 51,358,616 bytes (about 49 MiB) and
received HTTP 408 with a LiteSpeed HTML response. Upload readiness and status
requests succeeded. This is an upload-request timeout, not the unrelated
`import_proformas` missing-table error.

## Deploy both projects

Deploy the updated Laravel controller, job, routes, rate limiter, console
schedule, and new `app/Services/CertifyBundleChunkUpload.php`. Keep the scoped
LiteSpeed rules already in `public/.htaccess`. Then run from the live backend:

```sh
php artisan optimize:clear
php artisan route:list --path=certifyInvoices/import-bundle
```

The routes must include `prepare`, `chunk`, `start`, and `status`. Rebuild or
hot-reload Flutter with the updated repository and ZIP normalizer. No new
database migration is required for chunked uploads. The proforma migration,
if missing, is a separate deployment requirement.

## Expected behavior

- Flutter requests protocol version 3 and sends ZIP fragments of at most 256 KiB.
- Each fragment is checksummed and acknowledged before progress advances.
- HTTP 408 and temporary connection failures retry the same fragment, with a
  bounded retry count. Manual retry reuses the operation and skips confirmed parts.
- The final `start` request contains only the operation ID, not the ZIP.
- The worker assembles the ZIP using streams and verifies its size and SHA-256
  before invoking the existing transactional importer.
- ZIP normalization preserves timestamps, so retries reproduce identical bytes.
- Import authorization is unchanged. The chunk route alone has a separate
  per-user/IP rate limit so the normal 60-request API limit does not interrupt
  large uploads. Other API routes retain their existing limit.

PHP must be able to write `storage/app/certify-import-queue`,
`storage/app/certify-import-status`, and `storage/app/certify-import-chunks`.
PHP file/POST limits apply to each fragment; the negotiated part size is reduced
if necessary. The complete archive limit is 512 MiB. Ensure enough free disk
space for both staged fragments and the assembled/extracted archive.

Fragments are removed after processing. The standard Laravel scheduler also
runs `certify:prune-import-uploads` daily to remove abandoned staging whose
24-hour status has expired. This requires the normal `schedule:run` cron entry;
the command can also be run manually. It never removes active upload staging
or the original ZIP on the user's computer.

## Hosting follow-up

If even small fragments receive 408, ask hosting support to investigate
LiteSpeed connection/request-body timeouts for these import routes, using
the time and request ID displayed by Flutter. Increasing PHP
`max_execution_time` alone does not address every web-server timeout.

Official guidance:
- https://docs.litespeedtech.com/lsws/cp/cpanel/long-run-script/
- https://docs.litespeedtech.com/lsws/troubleshooting/#large-file-upload-failures

Do not paste authorization headers into diagnostics. Revoke any bearer token
that was exposed in a screenshot.
