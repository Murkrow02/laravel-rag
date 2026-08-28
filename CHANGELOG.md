# Changelog

All notable changes to `murkrow/laravel-rag` are documented here.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.1.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

Patch releases are cut automatically on every push to `main`; minor and major
releases are tagged by hand.

## [Unreleased]

## [1.0.0] - 2026-08-28

### Added

- Initial public release, extracted from a private application.
- Configuration-driven sources: `EloquentSource`, `GroupedEloquentSource`,
  `ClosureKnowledgeSource`, and typed filters.
- Sentence-aligned sliding-window chunker with cross-page bridging, OCR
  handling and deterministic hashing for cheap incremental re-indexing.
- Embeddings and generation through [Prism](https://prismphp.com).
- `PgVectorStore` retrieval pipeline: over-fetch, optional lexical fusion,
  score floor, de-duplication, MMR, neighbour expansion.
- Grounded answering with `[#n]` citations and a publishable Blade prompt.
- Optional Filament control panel, MCP server and standalone chat page.

[Unreleased]: https://github.com/Murkrow02/laravel-rag/compare/v1.0.0...HEAD
[1.0.0]: https://github.com/Murkrow02/laravel-rag/releases/tag/v1.0.0
