# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [0.1.0-alpha] - 2025-06-04

### Added
- Initial alpha release
- Core Live Inputs API functionality:
  - List Live Inputs (`listLiveInputs()`)
  - Create Live Input (`createLiveInput()`)
  - Get Live Input (`getLiveInput()`)
  - Update Live Input (`updateLiveInput()`)
  - Delete Live Input (`deleteLiveInput()`)
- Support for both API Token and API Key authentication
- Laravel Service Provider with publishable configuration
- Facade support for convenient static access (`Stream::`)
- Custom exception handling:
  - `AuthenticationException`
  - `ValidationException`
  - `NotFoundException`
  - `CloudflareStreamApiException` (base)
- Comprehensive test coverage:
  - Unit tests with HTTP client mocking
  - Feature tests for Laravel integration
  - Integration tests with real Cloudflare API
- Laravel 11.0+ and 12.0+ compatibility
- PHP 8.1+ support

### Notes
- This is an alpha release - API may change before stable 1.0
- Currently supports Live Inputs only (other Stream resources planned for future releases)
