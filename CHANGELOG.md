# CHANGELOG

## Version 0.5.1-beta - 2023-02-03

### Changed

- Changed all `$_ENV` tasks to `getenv()`.

---

## Version 0.5.0-beta - 2023-02-02

### Changed

- Updated `ConnectMySQL` to consistantly get it's database parameters from `$_ENV`.

---

## Version 0.4.9-beta - 2022-12-09

### Fixed

- Fixed a minor bug with environment loading.

---

## Version 0.4.8-beta - 2022-11-28

### Fixed

- Fixed bug with Forms `break` throwing warning.
- Fixed a situation where session starts still occasionally throw errors.

---

## Version 0.4.7-beta - 2022-11-08

### Fixed

- Fixed a bug where errors would be thrown if array keys were not sequential when dealing with SQL `IN()` and `NOT IN()`.

---

## Version 0.4.6-beta - 2022-11-04

### Changed

- Added magic method `__isset` to GetSet. This allows for calling `isset` on `__set` properties.

---

## Version 0.4.5-beta - 2022-11-04

### Fixed

- Fixed a bug with the `__get` method in `GetSet`.

---

## Version 0.4.4-beta - 2022-11-04

### Added

- Added trait `GetSet`.

---

## Version 0.4.3-beta - 2022-11-04

### Added

- Added error `UndefinedProperty`.

---

## Version 0.4.2-beta - 2022-11-03

### Added

- Added email validation method.

---

## Version 0.4.1-beta - 2022-11-02

### Changed

- Revamped `Draw::section_break` to much more gracefully draw in a new section, adding title and description as parameters. Note this is a breaking change from how this method was called before if parameters were parsed.

--

## Version 0.4.0-beta - 2022-10-28

### Added

- Added a Javascript Class tool called `SVGTool` for generating SVG images on the fly.

---

## Version 0.3.4-beta - 2022-10-27

### Changed

- Added param `$params` to `Forms` method `include_exclude_columns`.

---

## Version 0.3.3-beta - 2022-10-25

### Added

- Added error `LogPathNotSet`.

### Changed

- Revamped ConnectMySQL so that the logging path is not set by default, and if one is not explicitly set, an exception is thrown. #47

### Packages Updated

| Package | Old | New |
| ------- | --- | --- |
| phpmailer | v6.6.4 | v6.6.5 |
| PHP Debuggin Tool | 1.0.5 | 1.0.7 |

### Issues Closed

- #47

---

## Version 0.3.2-beta - 2022-10-25

### Added

- Added basic Captcha tools.
- Added validation tools for South African ID number.

---

## Version 0.3.1-beta - 2022-10-24

### Added

- Added error page `InvalidErrorCode`.

---

## Version 0.3.0-beta - 2022-10-20

### Added

- Added a basic but functional init tool. Can be used to set up a new application with a single run. #5

### Changed

- Added ordering by `ORDINAL_POSITION` in `ConnectMySQL` method `get_table_columns`.
- Revamped `ConnectMySQL` to better handle error logs. Added a param to `set_debug_mode`, to manually set a logs path.

### Packages Updated

| Package | Old | New |
| ------- | --- | --- |
| phpmailer | 6.6.4 | 6.6.5 |
| PHP Debuggin Tool | 1.0.6 | 1.0.7 |

### Issues Closed

- #5

---

## Version 0.2.3-beta - 2022-10-19

### Added

- Added methods `copy_file` & `move_file` to the `FileSystem` class.

---

## Version 0.2.2-beta - 2022-10-18

### Changed

- Improved with how `Session::start()` detects if a session has started. Removed param `$hide_session_start_info`.

### Fixed

- Fixed a bug with `Forms::content_drawer_arrow`.

---

## Version 0.2.1-beta - 2022-10-17

### Changed

- Revamped `Auth/Session::start` to function better.

### Fixed

- Fixed a bug with `Forms::content_drawer_arrow`.

---

## Version 0.2.0-beta - 2022-10-05

### Added

- Enum `DrawError` for defining different ways of displaying an error.
- Class `ErrorExceptionHandler` for custom handling the custom displaying of error messages. This includes the writing of logs #17
- Interface for custom Exception classes `ExceptionInterface`.
- Abstract Meta Class `ExceptionMeta` for extending to when creating custom Exception classes. All new custom Exceptions should extend to this.

### Changed

- Changed `FileSystem::create_blank_file` to use the built in php function `touch`.
- Renamed and categorized the various Custom Exception classes.

### Packages Updated

| Package | Old | New |
| ------- | --- | --- |
| PHP Debuggin Tool | 1.0.5 | 1.0.6 |

### Issues Closed

- #17

---

## Version 0.1.13-beta - 2022-09-29

### Fixed

- Fixed a bug with generating CRON files, calling the `Tools\Mail` classes.

### Changed

- Moved some specific methods in `CronHandler` back to LRS. Full overhall still required.

---

## Version 0.1.12-beta - 2022-09-29

### Changed

- Converted function `file_upload_max_size`and `parse_size` into static method of `src/Tools/Upload/UploadHandler.php`.

---

## Version 0.1.11-beta - 2022-09-27

### Added

- Added new Error handling files:
  - `FileNotWriteableError`
  - `UniqueValueDulicateException`

### Changed

- Added the option `BETWEEN` to SQL queries.

---

## Version 0.1.10-beta - 2022-09-20

### Changed

- Added a default margin 5. _temp fix for LPA_

---

## Version 0.1.9-beta - 2022-09-20

### Changed

- Changed how header margins are set.

---

## Version 0.1.8-beta - 2022-09-19

### Fixed

- Fixed a bug with `LoadEnvironment` class in the Windows Environment.

---

## Version 0.1.7-beta - 2022-09-18

### Added

- Added class for performing autoloading tasks. #26

### Removed

- Removed funtion `load_class`.

### Issues Closed

- #26

---

## Version 0.1.6-beta - 2022-09-14

### Added

- Added `Auth\LoginHandler`.
- Added new `HTMLElements` method `a` for inserting links.
- Added method `file_exists` to `FileSystem`.
- Added a tool for inline setting echo for the specific object being drawn. #23
- Added methods for more easily changing the value of `class::$echo`.
- Added Exception Class `MethodNotFound`.
- Added meta methods for rendering HTML elements and consolidated current methods into these methods. These methods are:
  - `html_element_container`.
  - `html_tag_open`.
  - `html_tag_close`.
  - `assign_key_values`.

### Changed

- Extended the following to `HTML\HTMLMeta`: #23
  - `HTML\Buttons`
  - `HTML\Draw`
  - `HTML\Forms`
  - `HTMLElements`
  - `HTML\Scripts`

### Fixed

- Fixed a bug with `HTML::p_container`.
- Fixed a bug with header styles.

### Deprecated

- `HTMLElements` method `link`.

### Issues Closed

- #23

---

## Version 0.1.5-beta - 2022-09-09

### Added

- Added Meta class `HTMLMeta`.
- Added methods `ul` and `ol` for drawing out lists.
- Added maintenance mode css.

### Changed

- Revamped various HTML tag drawing to be more simple and consistant.
- Revamped `ConnectMySQL` in the following ways:
  - `connect_db()` always returns bool and sets `$this->conn` directly.
  - `connect_db()` errors now gets placed on `$this->last_error`.
  - Optimized `set_db_year()` method to only execute `$this->connect_db` once.
  - Added method `database_exists`.

### Fixed

- Fixed bug with ConnectMySQL `get_tables`, `get_table_columns`, `get_table_columns_schemas` searching for date.
- Fixed CSS import.

---

## Version 0.1.4-beta - 2022-09-07

### Added

- Error handling classes
  - `InvalidInputException`.
  - `MissingRequiredInputException`.
  - `FileNotFoundError`.
  - `ConstantAlreadyDefinedError`.
- Added `LBF\Tools\Env\LoadEnvironment` class #18
- Added `LBF\Tools\Routing\Routing` class #4

### Issues Closed

- #4
- #18

---

## Version 0.1.3-beta - 2022-09-06

### Added

- Added function `timestamp_cache_validation` to functions.php.

---

## Version 0.1.2-beta - 2022-09-05

### Added

- Added css file `sizes.css` for root sizes variables.
- Added class `LBF\Auth\Cookie` for standardized handling of cookies. #12

### Fixed

- Fixed an incorrect import in the `floating_top_bottom_buttons` Button.
- Fixed a bug with LDAPHandler, causing false authentications.

### Issues Closed

- #12

---

## Version 0.1.1-beta - 2022-09-01

## Added

- This CHANGELOG.
- Migrated in the following JS Libraries: #7
  - `ajax.js`
  - `datetime.js`
  - `filter.js`
  - `forms.js`
  - `hash.js`
  - `input_validation.js`
  - `loading.js`
  - `modal.js`
  - `mutations.js`
  - `print.js`
  - `responses.js`
  - `spreadsheetTool.js`
  - `table_filters.js`
  - `tools.js`
  - `ui.js`
  - `uploader_element.js`
  - `uri.js`
  - `validation.js`
- Migrated in the following CSS Libraries & Fonts & themes: #7
  - `theme\default.css`
  - `lib\basic-tools.css`
  - `lib\buttons.css`
  - `lib\form-elements.css`
  - `lib\input-elements.css`
  - `lib\input-validation.css`
  - `lib\loaders.css`
  - `lib\modal.css`
  - `lib\table.css`
  - `lib\template.css`
  - `font\NunitoEB.css`
  - `Roboto.css`
  - `UbuntuMono.css`
- Added `lourie-basic-framework.css` as a loader. #7

### Fixed

- Fixed a bug in `HTML\Forms` callStatic method, where a text type was being called multiple times.
- Fixed a bug with left / right arrow columns. Arrows pointing the wrong way.

### Issues Closed

- #7

---

## Version 0.1.0-beta - 2022-08-22

- Added all the PHP functions used in Lourie Registration System, generalizing them as needed so that they can be used in multiple projects.
