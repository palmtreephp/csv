# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [5.1.0] - 2026-11-15

### Added

- `ExternalReader` accepts a stream context as its third argument, e.g. to set
  a timeout or request headers.
- `Writer::write()`, `Writer::setData()`, `Writer::addRows()` and
  `Downloader::download()` accept any iterable, so generators can write large
  data sets without loading them into memory.
- `Writer::setEscapeFormulas()` prefixes headers and string values that start
  with `=`, `+`, `-`, `@`, tab or carriage return with a single quote, to
  protect spreadsheet users from CSV injection. Off by default.
- `StringUtil::escapeFormulas()`.
- `NumberNormalizer::nullable()` returns `null` for non-numeric values,
  including empty cells, instead of `0`.
- `Writer::writeTo()` copies the CSV to a stream in chunks, e.g. to build a
  Symfony or PSR-7 response from a `Downloader`.
- Documentation for the whole API in [`docs/`](docs/index.md), linked from the
  README.

### Changed

- `Downloader` sends `Content-Type: text/csv; charset=utf-8` instead of
  `text/csv`.

### Fixed

- Writing a nested array as a cell value throws `InvalidArgumentException`
  instead of writing `Array` and raising a warning. `StringUtil::escapeEnclosure()`
  no longer accepts nested arrays.
- Reading a file with duplicate headers throws `UnexpectedValueException`
  instead of silently overwriting the earlier column's values.

### Security

- `ExternalReader` only accepts `http` and `https` URLs, so a user-supplied URL
  can't be used to read local files through `file://`, `php://`, `phar://` or a
  plain path. Use `Reader` for local files.

## [5.0.0] - 2026-11-13

### Breaking changes

- `CsvFileObject::trimFinalLineEnding()` is removed. Written files never end
  with a line ending, so there is nothing to trim.
- Headers are written lazily: before the first row, when contents are read, on
  download, or when the document is closed. Changing headers after they have
  been written throws `LogicException`.
- Writing an associative row with a key that isn't in the headers throws
  `InvalidArgumentException`. Missing keys are written as empty cells.
- Writing a value that isn't a scalar, `null` or `Stringable` throws
  `InvalidArgumentException`.
- Accessing a missing cell on a `Row` throws `OutOfBoundsException`.

### Added

- Rows with headers can be destructured by position, e.g.
  `foreach ($reader as [$name, $price])`.
- `Row::getCell()`.
- `addNormalizer()` accepts integer keys.

### Fixed

- Associative rows are written in header order rather than their own key order.
- `addHeader()` after `setHeaders()` no longer writes a second header row, and
  `addRow([])` no longer recurses forever.
- Adding a row after `getContents()` no longer merges it onto the previous row.
- Writing int and float values no longer throws a `TypeError`.
- `Writer::setData([])` no longer throws a `TypeError`.
- Blank lines and rows containing a single empty quoted cell no longer stop
  reading early; they are skipped.
- The header normalizer is only applied to the header row, so it no longer
  overrides normalizers added for integer keys or applies to extra columns.
- `setFilePath()` takes effect when the document is already open, and the CSV
  control setters no longer open (or, for writers, truncate) the file.
- `NumberNormalizer` no longer returns `0` for valid numbers such as `-05`,
  `+5`, `.5`, `5.` and `00.5`.
- `DateTimeNormalizer` no longer fills fields missing from the format with the
  current time, and returns `null` for invalid dates such as `2024-02-30`
  instead of rolling them over.
- `BooleanNormalizer::caseSensitive(true)` is respected when called after
  `pairs()`.
- `CallableNormalizer` accepts a `null` inner normalizer.
- Normalizers throw a clear error when an inner normalizer returns a non-string.
- `Cell::__toString()` falls back to the raw value for values such as
  `DateTime` instead of throwing an `Error`.
- `ExternalReader` deletes its temporary copy of the remote file.
- Quotes and non-ASCII characters in download filenames no longer break the
  `Content-Disposition` header. An ASCII fallback is sent alongside an
  RFC 5987 `filename*`.

[5.1.0]: https://github.com/palmtreephp/csv/compare/v5.0.0...v5.1.0
[5.0.0]: https://github.com/palmtreephp/csv/compare/v4.0.1...v5.0.0
