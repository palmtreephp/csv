# Changelog

All notable changes to this project are documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

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

[5.0.0]: https://github.com/palmtreephp/csv/compare/v4.0.1...v5.0.0
