# Aislum Studio

A lightweight, portable web application for small office multimedia design, document management, and business card creation.

## Project Overview

Aislum Studio is designed to be a scalable, easy-to-deploy solution for small offices that need to design and manage documents, design business cards, and collaborate on multimedia projects. The application prioritizes simplicity, portability, and minimal resource requirements.

## Technology Stack

- **Domain and Hosting**: aislumstudio.com AT dreamhost.com
- **Access**: HTTPS
- **Frontend**: PICO CSS, jQuery
- **Backend**: PHP
- **Database**: MySQL, SQLite (for faster access)
- **File Storage**: Local filesystem
- **Version Control**: Git

## Project Structure

```
aislum-studio/
├── public/                  # Publicly accessible files
│   ├── css/                # Stylesheets (PICO CSS)
│   ├── js/                 # JavaScript files (jQuery)
│   ├── uploads/            # User-uploaded files
│   └── assets/             # Static assets (images, icons)
├── src/
│   ├── api/                # API endpoints (PHP)
│   └── views/              # Template files (PHP)
├── config/                 # Configuration files
├── database/               # Database schemas and migrations
├── README.md               # Project documentation
└── .gitignore              # Git ignore rules
```

## Features (MVP)

1. **User Authentication**: Secure login and registration
2. **Document Management**: Upload, organize, and search documents
3. **Business Card Designer**: Template-based card creation
4. **Document Template Editor**: Customize office templates
5. **File Storage**: Secure storage for designs and documents
6. **Basic Collaboration**: Share documents with team members

## Getting Started

### Prerequisites

- PHP 7.4 or higher
- MySQL 5.7 or higher (or SQLite 3)
- A web server (Apache, Nginx, or PHP built-in server)
- Git

### Installation

1. Clone the repository:
   ```bash
   git clone https://github.com/aislum-studio/aislum-studio.git
   cd aislum-studio
   ```

2. Set up the database:
   - Create a MySQL database or use SQLite
   - Run the database migrations (details in `database/` directory)

3. Configure the application:
   - Copy `config/config.example.php` to `config/config.php`
   - Update database credentials and settings

4. Start the development server:
   ```bash
   php -S localhost:8000
   ```

5. Open your browser and navigate to `http://localhost:8000`

## Development Roadmap

### Phase 1 (MVP)
- User authentication system
- Basic document management
- Business card designer
- SQLite database setup

### Phase 2
- Enhanced collaboration features
- Document versioning
- Advanced template editor
- MySQL integration

### Phase 3
- API for third-party integrations
- Mobile responsiveness improvements
- Performance optimization
- Export to multiple formats

## Contributing

Please follow the project structure and coding standards. All contributions should be submitted via pull requests.

## License

To be determined.

## Contact

For inquiries or support, please contact the Aislum Studio team.
