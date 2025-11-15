# Maxsoft MVC CMS v1.0.0

A modern, lightweight Content Management System (CMS) built with PHP and JavaScript, designed for flexibility and ease of use. This CMS follows SOLID principles and leverages an MVC architecture, utilizing PHP libraries like Laravel's Eloquent ORM, Twig templating, and a simple router, combined with a robust front-end setup using Webpack, Bootstrap, and jQuery.

**Current Version:** 1.0.0 (SOLID Refactoring Release)

## Architecture & SOLID Principles

This CMS has been refactored to follow SOLID design principles for better maintainability and testability:

- **Single Responsibility Principle (SRP)**: Each class has one reason to change
- **Open/Closed Principle (OCP)**: Classes are open for extension, closed for modification
- **Liskov Substitution Principle (LSP)**: Subtypes are substitutable for their base types
- **Interface Segregation Principle (ISP)**: Clients depend only on methods they use
- **Dependency Inversion Principle (DIP)**: High-level modules don't depend on low-level modules

### Service Layer Architecture

The application uses a service layer with dependency injection:
- **Services**: Business logic separated into focused, testable units
- **Interfaces**: Define contracts for services, enabling easy mocking and testing
- **Dependency Injection**: Controllers receive their dependencies through constructors
- **Separation of Concerns**: Views, business logic, and data access are properly separated

## Features

- **SOLID Architecture**: Clean, maintainable code following SOLID design principles.
- **Service Layer**: Business logic separated into focused, testable services.
- **Dependency Injection**: Loose coupling between components for better testability.
- **MVC Architecture**: Organized structure with Core and App namespaces for clean code separation.
- **Database Support**: Uses Laravel's Eloquent ORM (`illuminate/database`) for database interactions.
- **Templating**: Powered by Twig (`twig/twig`) for dynamic and secure template rendering.
- **Routing**: Simple and fast routing with `pecee/simple-router`.
- **Front-End**: Built with Bootstrap 5, jQuery, and Webpack for modern, responsive UI.
- **Additional Tools**:
  - PDF generation with `mpdf/mpdf`.
  - Image processing with `spatie/image`.
  - JWT authentication with `firebase/php-jwt`.
  - Environment configuration with `vlucas/phpdotenv`.
- **Development Tools**: Includes GrumPHP for code quality, PHPUnit for unit testing, and Webpack for asset bundling.

## Prerequisites

Before setting up the project, ensure you have the following installed:

- **PHP**: 8.1 or higher
- **Composer**: For PHP dependency management
- **Node.js**: 16.x or higher
- **npm**: For JavaScript dependency management
- **MySQL**: For database storage
- **Git**: For cloning the repository

## Installation

Follow these steps to set up the CMS locally:

1. **Clone the Repository**:
   ```bash
   git clone https://github.com/gmaxsoft/cms.git
   cd cms
   ```

2. **Install PHP Dependencies**:
   Run the following command to install the required PHP packages:
   ```bash
   composer install
   ```

3. **Install JavaScript Dependencies**:
   Install front-end dependencies using npm:
   ```bash
   npm install
   ```

4. **Set Up Environment**:
   Copy the `.env.example` file (if available) to `.env` and configure your environment variables:
   ```bash
   cp .env.example .env
   ```
   Update the `.env` file with your database credentials and other settings. Example configuration:
   ```env
   DEBUG=true
   DEFAULT_BLOCK_LANG=1
   DB_DRIVER=mysql
   DB_HOST=localhost
   DB_NAME=YOUR OWN DATABASE NAME
   DB_USER=root
   DB_PASS=
   FRONTEND_URL=http://localhost:3000
   SECRET_KEY=your-secure-secret-key
   DEFAULT_BLOCK_LANG = 1
   SLIDERFILESPATH = 'upload/sliderImg/'
   ```

5. **Set Up the Database**:
   Create a MySQL database named `YOUR OWN DATABASE NAME` (or your preferred name, matching the `DB_NAME` in `.env`). To start the database installation process, enter `http://localhost:3000/install.php`. Follow the instructions.
   ```

6. **Build Front-End Assets**:
   Compile and bundle the front-end assets using Webpack:
   ```bash
   npm run build
   ```

7. **Start the Development Server**:
   Start the Webpack development server for front-end development:
   ```bash
   npm run dev
   ```
   Alternatively, serve the PHP application using a local server (e.g., PHP's built-in server):
   ```bash
   php -S localhost:8000 -t public
   ```

8. **Access the CMS**:
   Open your browser and navigate to `http://localhost:3000` for the front-end or `http://localhost:8000` for the PHP server, depending on your setup.

## Usage

- **Admin Panel**: Access the CMS admin panel (if implemented) via the configured `FRONTEND_URL`.
- **Customizing Templates**: Modify Twig templates in the `app/views` directory (assumed based on standard MVC structure).
- **Adding Routes**: Define custom routes in the `app` directory using `pecee/simple-router`.
- **Asset Management**: Update SCSS files in `public/scss` or JavaScript files in `public/js` and rebuild assets with `npm run build`.

## Project Structure

```
cms/
├── app/                    # Application logic
│   ├── Controllers/        # HTTP request handlers (uses services)
│   ├── Models/            # Eloquent ORM models
│   ├── Services/          # Business logic services
│   │   ├── Interfaces/    # Service contracts (SOLID interfaces)
│   │   ├── *.php          # Service implementations
│   └── Views/             # Twig templates
├── core/                   # Core CMS functionality (refactored with SOLID)
├── public/                 # Publicly accessible files
│   ├── js/                 # JavaScript source files
│   ├── scss/               # SCSS source files
├── dist/                   # Compiled front-end assets
├── vendor/                 # Composer dependencies
├── node_modules/           # npm dependencies
├── .env                    # Environment configuration
├── composer.json           # PHP dependencies
├── package.json            # JavaScript dependencies
├── webpack.config.js       # Webpack configuration
└── README.md               # Project documentation
```

## Service Architecture Details

### Core Services
- **SliderService**: CRUD operations for sliders
- **SliderFileService**: File management and image processing
- **SliderConfigService**: Configuration management
- **ImageProcessor**: Image manipulation and optimization
- **FileUploadService**: File validation and upload handling
- **LanguageService**: Multi-language support
- **TextUtilities**: Text processing utilities
- **TemplateRenderer**: Twig template rendering

### Dependency Injection
Controllers receive services through constructor injection, enabling:
- Easy unit testing with mocks
- Loose coupling between components
- Flexible service implementations
- Better separation of concerns

### Benefits of SOLID Refactoring
- **Testability**: Each service can be tested in isolation
- **Maintainability**: Changes are localized to specific services
- **Extensibility**: New features can be added without modifying existing code
- **Readability**: Clear separation of responsibilities
- **Reusability**: Services can be reused across different controllers

## Testing

This project includes comprehensive unit tests powered by PHPUnit 11 to ensure SOLID principles compliance and code quality.

### Test Structure
```
tests/
├── bootstrap.php              # Test environment setup
├── Unit/
│   ├── Services/             # Service layer tests
│   │   ├── SliderServiceTest.php
│   │   ├── TextUtilitiesTest.php
│   │   └── FileUploadServiceTest.php
│   └── Controllers/          # Controller tests with mocks
│       └── SliderControllerTest.php
```

### Test Coverage
- **Service Layer**: Business logic testing with isolated units
- **Controller Layer**: HTTP request/response testing with mocked dependencies
- **Validation**: Input validation and error handling
- **Integration**: Service interaction testing

### Key Testing Features
- **Mock Objects**: PHPUnit mocks for dependency isolation
- **Data Providers**: Parameterized tests for comprehensive coverage
- **Test Doubles**: Stubs and mocks for external dependencies
- **Assertion Methods**: Rich set of assertions for behavior verification

## Development

- **Code Quality**: Use GrumPHP for automated code quality checks:
  ```bash
  composer run-script grumphp
  ```
- **Unit Testing**: Run PHPUnit tests to ensure SOLID compliance:
  ```bash
  ./vendor/bin/phpunit
  ```
- **Test Coverage**: Generate coverage report:
  ```bash
  ./vendor/bin/phpunit --coverage-html reports/
  ```
- **Run Specific Tests**: Execute tests for specific components:
  ```bash
  # Test services only
  ./vendor/bin/phpunit tests/Unit/Services/

  # Test controllers only
  ./vendor/bin/phpunit tests/Unit/Controllers/

  # Test single file
  ./vendor/bin/phpunit tests/Unit/Services/SliderServiceTest.php
  ```
- **Front-End Development**: Run `npm run dev` for live reloading during development.
- **Production Build**: Generate optimized assets for production:
  ```bash
  npm run build
  ```

## Contributing

Contributions are welcome! Please follow these steps:

1. Fork the repository.
2. Create a new branch (`git checkout -b feature/your-feature`).
3. Make your changes and commit (`git commit -m 'Add your feature'`).
4. Push to the branch (`git push origin feature/your-feature`).
5. Open a pull request on GitHub.

Report bugs or suggest features via the [GitHub Issues](https://github.com/gmaxsoft/cms/issues) page.

## License

This project is licensed under the ISC License. See the [LICENSE](https://github.com/gmaxsoft/cms/blob/main/LICENSE) file for details.

## Contact

For questions or support, contact the author:
- **Name**: Maxsoft
- **Email**: 133238725+gmaxsoft@users.noreply.github.com
- **GitHub**: [gmaxsoft](https://github.com/gmaxsoft)