# Page Studio HTML Editor

A modern, full-featured HTML editor that combines CKEditor 5 Superbuild with a Material Design shell, autosave flows, version history, and a media library backed by PHP endpoints. The project is designed for local development on macOS and deployment through Coolify with persistent storage volumes.

## Key Features

- **CKEditor 5 Superbuild** with curated toolbar (font controls, media embed, code blocks, source editing)
- **Material Design UI** with light/dark mode toggle
- **Autosave & Version History** stored in browser local storage
- **Media Library** with drag-and-drop uploads (`upload-media.php`) and gallery listing (`media-list.php`)
- **Server-side snapshot saving** to `storage/snapshots` via `save-html.php`
- **Live Preview** loading saved HTML in a modal (`preview.php`)
- **Custom Templates & Components** for rapid content creation
- **Floating autosave indicator** with color-coded states for saving/saved/error

## Directory Structure

```
custom-html-editor/
├── index.html              # Main application shell and client logic
├── material-design.css     # Theme variables, layout, dark-mode overrides
├── storage/
│   ├── uploads/            # Media assets (gitignored)
│   ├── snapshots/          # HTML exports (gitignored)
│   └── logs/               # Future log output (gitignored)
├── database/
│   └── cms.sqlite          # SQLite database location (gitignored)
├── save-html.php           # Saves HTML content to snapshots directory
├── preview.php             # Displays saved HTML snapshots
├── upload-media.php        # Handles media uploads
├── media-list.php          # Lists uploaded media as JSON
├── media.php               # Serves uploaded media files
├── cms-ground-plan.md      # Product roadmap and architecture notes
└── clean-up-app.md         # Audit of UI/UX improvements and next steps
```

All storage directories and the SQLite database path are gitignored to keep the repository clean across environments.

## Requirements

- PHP 8.1+
- Node/npm (optional, only if you plan additional tooling)
- macOS or Linux workstation (tested on macOS)

## Local Development

1. **Start PHP server**
   ```bash
   php -S 127.0.0.1:8000
   ```
2. Open `http://127.0.0.1:8000/index.html` in your browser.
3. Ensure `storage/uploads`, `storage/snapshots`, and `storage/logs` exist. The app auto-creates them if missing.

### Testing Media Uploads
- Use the media sidebar dropzone or the toolbar button to upload images.
- Uploaded files are saved to `storage/uploads` and listed via `media-list.php`.
- `media.php` serves files securely so HTML references remain consistent between local dev and Coolify.

### Saving Pages
- Click **Save to Server** to write an HTML snapshot to `storage/snapshots`.
- Use **Preview** to open the snapshot inside the modal.

### Autosave & Status Indicator
- Autosave runs every few seconds after content changes.
- The floating chip in the bottom-right corner shows **Saving**, **Saved**, or **Save failed** states with color-coded feedback.

## Deployment (Coolify)

1. Map persistent volumes:
   - `/app/storage/uploads`
   - `/app/storage/snapshots`
   - `/app/storage/logs`
   - `/app/database/cms.sqlite`
2. Ensure environment variables (see `cms-ground-plan.md` section 13 & 15) are configured via Coolify secrets.
3. Deploy the PHP container; Coolify will persist user-generated content across releases.

## Troubleshooting

| Issue | Fix |
|-------|-----|
| Text unexpectedly appears white | Hard-refresh the page. CKEditor now restricts document colors and CSS forces non-white defaults on white backgrounds. |
| Media upload fails with 413 error | Increase PHP `upload_max_filesize` & `post_max_size`. |
| Snapshots not visible in preview | Confirm `storage/snapshots` exists and has correct permissions. |
| CKEditor toolbar missing | Verify `vendor/ckeditor/ckeditor.js` exists; run curl command from README if needed. |

## Contributing Workflow

1. Create feature branch
2. Make changes & update docs/tests
3. Run formatting/linting if applicable
4. Commit with descriptive message
5. Push branch and open PR

---

For detailed future work, see **`clean-up-app.md`** which contains the comprehensive audit and roadmap completed on October 27, 2025.
