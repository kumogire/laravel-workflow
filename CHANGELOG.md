# Changelog

All notable changes to `laravel-workflow` will be documented in this file.

## [1.3.0] - 2024-04-02

### Added
- Admin auth middleware
- Tests User model

### Fixed
- CompleteStepRequest.php: Changed step_id from required to sometimes
- WorkflowService.php: Added implicit not_started → in_progress transition before completing a step (enforces valid state machine path)
- AdminApiTest.php: Corrected POST create assertions from 200 to 201


## [1.2.0] - 2024-02-07

### Added
- UI components
- Open API docs


## [1.1.0] - 2024-02-06

### Added
- Complete test suite with unit and feature tests
- Admin API endpoints for workflow management
- Step reordering functionality

### Fixed
- Permission checking for step completion

## [1.0.0] - 2024-01-10

### Added
- Initial release
- Workflow and step management
- State machine implementation
- Event-driven action system