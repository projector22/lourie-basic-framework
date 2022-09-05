# CHANGELOG

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
