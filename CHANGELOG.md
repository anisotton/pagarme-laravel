# Changelog

All notable changes to `pagarme-laravel` will be documented in this file.

## [1.0.0] - 2025-06-26

### Added
- 🚀 **Laravel 12 Support**: Complete compatibility with Laravel v12
- 🔧 **PHP 8.3+ Support**: Updated minimum PHP requirement to 8.3
- 📦 **Modern Package Structure**: Updated service provider and package structure
- 🎯 **Type Safety**: Added proper type hints and return types throughout the codebase
- 🏗️ **Readonly Classes**: Implemented readonly classes for contract objects
- 🧪 **Testing Framework**: Added PHPUnit 11 support with comprehensive test structure
- 📚 **Improved Documentation**: Updated README with detailed usage examples
- 🛠️ **Development Tools**: Added Laravel Pint for code formatting
- 🔒 **Enhanced Security**: Improved error handling and API communication
- 📋 **Command Line Tools**: Enhanced Artisan command for package information

### Changed
- ⬆️ **Dependencies**: Updated all dependencies to Laravel 12 compatible versions
- 🔄 **API Adapter**: Completely rewritten HTTP client wrapper with better error handling
- 🎨 **Code Style**: Modernized code to use PHP 8.3+ features and best practices
- 📁 **File Structure**: Organized contracts and endpoints with better separation of concerns
- 🔧 **Configuration**: Improved configuration file with better defaults

### Improved
- 🚄 **Performance**: Better HTTP client configuration with proper timeouts
- 🛡️ **Error Handling**: Enhanced exception handling with more descriptive messages
- 📖 **Code Documentation**: Added comprehensive inline documentation
- 🎯 **Type Safety**: Strict typing throughout the codebase
- 🧩 **Service Container**: Proper dependency injection configuration

### Technical Details
- **Breaking**: Requires PHP 8.3+ and Laravel 12+
- **Breaking**: Some method signatures have changed for better type safety
- **Breaking**: Namespace structure updated for better organization
- **Feature**: Added support for modern Laravel features and best practices
- **Feature**: Comprehensive test suite with Orchestra Testbench
- **Feature**: Modern PHPUnit configuration with code coverage

## [0.0.0] - Previous Version
- Initial release with basic Pagar.me API integration
- Laravel 11 support
- Basic CRUD operations for customers, orders, charges, recipients, and subscriptions
