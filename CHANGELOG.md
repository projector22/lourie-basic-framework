# CHANGELOG

## Veriosn 0.1.7-beta - UNRELEASED

### Issues Closed

- #26 WIP

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
