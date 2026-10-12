# Public Instagram Gallery: deployment / proof

Issue #107. This feature targets the dedicated /galeri page only. Homepage motion gallery and admin GalleryItem CRUD stay unchanged.

## Required secrets (never commit)

1. **Revoke/regenerate** the Instagram access token exposed in chat. Treat the original token as compromised.
2. Configure \`INSTAGRAM_ACCESS_TOKEN\` in the production Laravel .env on the server (not Vite, not JS). Use an authenticated Instagram Creator account with media read permission. No need for DM, publishing, comments, or insight permissions for this feature.
3. Set the existing \`MEDIA_DISK=s3\` and Cloudflare R2 environment variables with the canonical \`MEDIA_PUBLIC_URL\`. Media copied by this job is served from that R2 public domain, not directly from Instagram CDN.

## After pulling

\`\`\`bash
php artisan migrate --force
php artisan config:clear
php artisan instagram:sync-gallery
php artisan schedule:list
\`\`\`

Check /galeri after the sync command. The command prints only counters, never the token. Instagram source posts are copied into \`instagram_gallery_items\`; each media file is mirrored to the configured R2 disk. Existing \`gallery_items\` remain a separate manual section, managed in /admin/galeri.

## Scheduler

The application schedules \`instagram:sync-gallery\` hourly with overlap prevention. On the hosting system, ensure **exactly one** normal Laravel scheduler cron entry runs \`php /absolute/project/path/artisan schedule:run\` every minute. Do not create a second direct Instagram cron entry.

The Instagram token is expiring/rotatable and should be renewed before its expiry in Meta Developer; updating \`INSTAGRAM_ACCESS_TOKEN\` in the hosting environment requires reloading the config. Automatic token refresh is **not** implemented.

## Failure behavior

A missing/expired token or Meta API outage fails the sync command without breaking /galeri or deleting previously mirrored media. Individual invalid/broken/oversized posts are skipped with an ID and exception class in logs, without logging the token. Existing successfully synchronized files remain available through R2. Only latest 12 posts are polled each time; historical synced posts are retained. Carousel posts use the cover for the card and link to the original Instagram post for the remaining slides.

## Verification

\`\`\`bash
php artisan test --filter=InstagramGalleryTest
php artisan test --filter=PortalScaffoldTest
php artisan test --filter=SimpleGalleryCrudTest
\`\`\`
