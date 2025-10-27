# Comprehensive Ground Plan for the Mini CMS

## 1. Core Features (Excluding User Authentication)
- Create, edit, delete, and organize pages.
- Media library for uploading and managing images/files.
- Preview and publish functionality.
- CKEditor integration with advanced plugins (e.g., image upload, code blocks).
- Support for templates and reusable components.

## 2. Material Design Integration
- Use a Material Design library (e.g., Materialize, Angular Material).
- Ensure responsive design for mobile and desktop.
- Consistent UI/UX adhering to Material Design principles.

## 3. Dark Mode Toggle System
- Implement a toggle for switching between light and dark themes.
- Use CSS variables or Material Design theme system for color management.

## 4. Database Schema
- **Pages Table**:
  - `id`: Primary key.
  - `title`: Page title.
  - `content`: HTML content.
  - `created_at`: Timestamp.
  - `updated_at`: Timestamp.
- **Media Table**:
  - `id`: Primary key.
  - `file_name`: Name of the file.
  - `file_path`: Path to the file.
  - `uploaded_at`: Timestamp.

## 5. Architecture Diagram
- **Frontend**:
  - Material Design-based UI.
  - CKEditor for content editing.
- **Backend**:
  - PHP for handling requests.
  - MySQL for database management.
- **Storage**:
  - File system for media storage.
  - Database for metadata.
- **API**:
  - RESTful API for managing content programmatically.

## 6. Wireframes
- **Dashboard**:
  - Navigation menu for pages, media, and settings.
  - Overview of recent activity.
- **Editor**:
  - CKEditor interface with toolbar.
  - Sidebar for templates and media.
- **Media Library**:
  - Grid view of uploaded files.
  - Upload button with drag-and-drop support.

## 7. Deployment and Scalability
- Deploy using Coolify for containerized environments.
- Use Docker for consistent development and production environments.
- Plan for horizontal scaling with load balancers.
- Optimize database queries for performance.

## 8. Advanced Editing Features
- **Image Management**:
  - Drag-and-drop image upload directly into the editor.
  - Resize, crop, and align images within the editor.
  - Support for image captions and alt text for accessibility.
- **Rich Text Formatting**:
  - Advanced text styling options (e.g., font family, font size, text color).
  - Support for tables, lists, and block quotes.
- **Code Blocks**:
  - Syntax highlighting for multiple programming languages.
  - Inline code snippets with formatting options.
- **Version Control**:
  - Track changes made to pages with the ability to revert to previous versions.
  - Display a history of edits for collaborative workflows.

## 9. Media Library Enhancements
- **Categorization**:
  - Organize media files into folders or categories.
- **Search and Filter**:
  - Search by file name, type, or upload date.
  - Filter by categories or tags.
- **Preview and Metadata**:
  - Preview images, videos, and documents before inserting them.
  - Display metadata such as file size, dimensions, and upload date.

## 10. Accessibility Features
- Ensure all UI components are keyboard-navigable.
- Provide ARIA labels for screen readers.
- Include a contrast checker for color themes.
- Support for high-contrast mode for visually impaired users.

## 11. Collaboration Tools
- **Real-Time Collaboration**:
  - Allow multiple users to edit the same page simultaneously.
  - Display user cursors and changes in real-time.
- **Comments and Feedback**:
  - Add inline comments to specific sections of a page.
  - Resolve or archive comments after addressing them.

## 12. Deployment and Scalability (Expanded)
- **Load Balancing**:
  - Use Nginx or HAProxy for distributing traffic.
- **Database Optimization**:
  - Implement read replicas for scaling database reads.
- **Monitoring and Alerts**:
  - Use tools like Prometheus and Grafana for monitoring system health.
  - Set up alerts for high CPU usage, memory consumption, or downtime.
## 8. Security Measures
- Implement input validation and sanitization to prevent XSS and SQL injection.
- Use CSRF tokens for form submissions.
- Secure file uploads by validating file types and limiting file sizes.
- Enforce HTTPS for all communications.

## 9. API Endpoints
- **GET /pages**: Retrieve a list of pages.
- **POST /pages**: Create a new page.
- **PUT /pages/{id}**: Update an existing page.
- **DELETE /pages/{id}**: Delete a page.
- **GET /media**: Retrieve a list of media files.
- **POST /media**: Upload a new media file.

## 10. Testing Strategy
- Unit testing for PHP scripts using PHPUnit.
- Integration testing for API endpoints.
- End-to-end testing for the CMS interface using Cypress.

## 11. Performance Optimization
- Implement caching for frequently accessed data.
- Optimize database queries with proper indexing.
- Minify CSS and JavaScript assets for faster load times.

## 12. Roadmap
- **Phase 1**: Core features and database schema implementation.
- **Phase 2**: Material Design integration and dark mode toggle.
- **Phase 3**: API development and security measures.
- **Phase 4**: Testing and performance optimization.
- **Phase 5**: Deployment and scalability enhancements.

This document outlines the foundational plan for the mini CMS, ready for review and further refinement.

## 13. Environment Strategy & Deployment
- Maintain separate configuration for local development (developer machines, Docker compose) and production (Coolify-managed containers).
- Track all environment-specific settings in `.env` files, committed as `.env.example` with non-sensitive defaults.
- Use Git workflows to gate changes (feature branches → PR → main) before Coolify deploys to production.
- Automate database migrations and cache clears during Coolify deployments to keep environments in sync.

## 14. Subresource Integrity (SRI) Guidance
- Subresource Integrity lets browsers verify CDN-delivered files using cryptographic hashes to prevent tampering.
- Only include an `integrity` attribute when the hash exactly matches the CDN asset version being loaded; mismatches cause the browser to block the file.
- When hashes are unknown or assets change frequently, omit the `integrity` attribute or host the dependency locally.
- Document each external asset (URL, version, hash) in deployment notes to simplify verification.

## 15. Storage & Folder Structure
- Standardize persistent directories under `/app/storage`:
  - `/app/storage/uploads` — media uploads used inside documents.
  - `/app/storage/snapshots` — generated HTML exports for previews or publication.
  - `/app/storage/logs` — application logs and diagnostics.
- Keep the SQLite database at `/app/database/cms.sqlite`, mounting the file as a volume in Coolify and local Docker.
- Provide `storage/README.md` documenting permissions, cleanup cadence, and backup strategy; ensure all storage paths are gitignored.
- Plan for a future abstraction to object storage (S3-compatible) by storing public URLs and metadata in the `media` database table.

## 16. Operational Checklist
- [ ] Provide `.env.example` outlining DB credentials, storage paths, CKEditor keys, and feature toggles.
- [ ] Configure Coolify pipelines for staging/production with health checks and automatic rollback on failure.
- [ ] Implement centralized logging (e.g., Monolog + Loki) capturing upload/save errors.
- [ ] Schedule recurring backups for `uploads/` and the CMS database; document restore procedures.

### Database Schema for Pages and Media

#### Pages Table
- **id**: Primary key, auto-increment.
- **title**: VARCHAR(255), title of the page.
- **content**: TEXT, HTML content of the page.
- **created_at**: TIMESTAMP, creation date.
- **updated_at**: TIMESTAMP, last update date.

#### Media Table
- **id**: Primary key, auto-increment.
- **file_name**: VARCHAR(255), name of the media file.
- **file_path**: VARCHAR(255), path to the file on the server.
- **uploaded_at**: TIMESTAMP, upload date.

#### Relationships
- Pages can reference media files via a many-to-many relationship (e.g., a pivot table `page_media`).

#### Next Steps
- Implement these tables in the database.
- Create PHP scripts for CRUD operations.