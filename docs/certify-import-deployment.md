# Certify data, client/cheque media, and Sales ZIP uploads on production

The observed failed request contained 51,358,616 bytes (about 49 MiB) and
received HTTP 408 with a LiteSpeed HTML response. Upload readiness and status
requests succeeded. This is an upload-request timeout, not the unrelated
`import_proformas` missing-table error.

## Deploy both projects

Deploy the complete updated Laravel backend, including the shared
`BundleUploadController`, `MediaBundleUploadController`,
`SalesBundleUploadController`, `SalesBundleArchive`, `BundleImportProgress`,
the updated import job and domain controllers, routes, and `public/.htaccess`.
Keep the existing chunk/status storage services, upload rate limiter, and
pruning schedule. Then run from the live backend:

```sh
php artisan optimize:clear
php artisan route:list --path=certifyInvoices/import-bundle
php artisan route:list --path=certifyInvoices/import-media-bundle
php artisan route:list --path=pos/sales/import-bundle
```

Each route group must include `prepare`, `chunk`, `start`, and `status`. Rebuild
and deploy Flutter with the shared resumable transport, updated repositories,
screens, and ZIP normalizer. Do not deploy only the former Certify controller:
its transport now lives in the shared controller. No new
database migration is required for chunked uploads. The proforma migration,
if missing, is a separate deployment requirement.

## Expected behavior

- Flutter requests protocol version 3 and sends ZIP fragments of at most 256 KiB.
- Each fragment is checksummed and acknowledged before progress advances.
- If a browser or LiteSpeed drops a multipart fragment without an HTTP
  response, Flutter retries that same fragment through the raw-binary chunk
  endpoint. The same checksum, owner, import-kind, and rate-limit guards apply.
- HTTP 408 and temporary connection failures retry the same fragment, with a
  bounded retry count. Manual retry reuses the operation and skips confirmed parts.
- The final `start` request contains only the operation ID, not the ZIP.
- The worker assembles the ZIP using streams and verifies its size and SHA-256
  before invoking the existing transactional importer.
- ZIP normalization preserves timestamps, so retries reproduce identical bytes.
- Client/cheque media uses the same transport but invokes only the existing
  attachment importer. Reference clients/cheques are not created or overwritten.
  Missing attachments are reported while valid rows continue.
  Flutter sends the original media ZIP rather than rebuilding all PDFs/images
  in browser memory; collection aliases are resolved by the Laravel importer.
- Sales packages the selected CSVs into a deterministic ZIP, retains the target
  department and authenticated user, then invokes the existing sales CSV
  transaction. Returns remain in Sales, never Certify. The existing 20 MiB
  per-CSV limit remains in place.
- Operation IDs are bound to both the authenticated owner and import kind.
  Sales retries cannot change departments. A lost start response is recovered
  by polling the same operation rather than starting another import.
- Media and Sales show confirmed upload/server progress inline. A pending
  operation locks ZIP replacement (and Sales department changes) and provides
  Retry upload or Check import status as appropriate.
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
