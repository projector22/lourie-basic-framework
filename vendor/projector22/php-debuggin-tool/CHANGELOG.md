# PHP DEBUGGIN TOOLS - CHANGELOG

## Version 1.0.4 - 2022-05-16

### Changed

- Auto handle if a file name parsed contains the `.log` extension.
- Added a routine to handle full path parsing.

---

## Version 1.0.3 - 2022-04-27

### Changed

- Added Variable-length argument lists to `DisplayData->data()` method.

---

## Version 1.0.2 - 2022-04-27

### Added

- Added the following tools:
  - `Cmd`
  - `Js`
  - `Log`

### Changed

- Added a common file (`tests\common.php`) for the autoloading & other common functions needed by the test files.

---

## Version 1.0.1 - 2022-04-27

### Added

- Added `index.php` page to tests.

---

## Version 1.0.0 - 2022-04-26

### Added

- Added basic caller class `Debug`.
- Added the following tools:
  - `DisplayData`
  - `Lorium`
  - `Timing`
- Added basic test pages for each class.
