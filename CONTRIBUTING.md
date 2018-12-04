# Contributing
We appreciate your interest in this project. This document contains essential information, should you want or need to contribute to this project.

## Bug reporting
We encourage active collaboration, but before opening a new issue, go through the following checklist, and make sure that:

- You have **read** the [installation](docs/installation.md), [configuration](docs/configuration.md) and [recordable configuration](docs/recordable-configuration.md) sections of the documentation;
- The problem you are facing is **not** documented in the [troubleshooting](docs/troubleshooting.md) section;
- A [GitLab issue](https://gitlab.com/altek/accountant/issues) with the same or similar problem you're having, doesn't already exist in an **open** or **closed** state;

If after going through all the previous steps you still have an issue, feel free to [open a new issue](https://gitlab.com/altek/accountant/issues/new) using the [Bug](.gitlab/issue_templates/Bug.md) template.

**Make sure the bug report is properly filled.**

## Development discussion
For new features or improvements, open a new issue using the [Proposal](.gitlab/issue_templates/Proposal.md) template.

## Which Branch?
Pull requests containing bug fixes or new features should always be done against the `master` branch.

## Coding Style
This package follows the [PSR-2](https://www.php-fig.org/psr/psr-2/) coding style guide and the [PSR-4](https://www.php-fig.org/psr/psr-4/) autoloader standard.

### StyleCI
The [StyleCI](https://styleci.io) service is hooked into our CI pipeline, so you'll be notified of any styling issues while pushing code.

### PHPDoc
Here's a valid documentation block example:

```php
/**
 * Get Recordable properties (deciphered).
 *
 * @param bool $strict
 *
 * @throws \Altek\Accountant\Exceptions\AccountantException
 * @throws \Altek\Accountant\Exceptions\DecipherException
 *
 * @return array
 */
protected function getDecipheredProperties(bool $strict = true): array
{
    // ...
}
```

### Committing to git
Each commit **MUST** have a proper message describing the work that has been done.
This is called [Semantic Commit Messages](https://seesparkbox.com/foundry/semantic_commit_messages).

Here's what a commit message should look like:

```txt
feat(Ledger): throw AccountantException in getProperty() if key doesn't exist
^--^ ^----^   ^-------------------------------------------------------------^
|    |        |
|    |        +-> Description of the work done.
|    |
|    +----------> Scope of the work.
|
+---------------> Type: chore, docs, feat, fix, hack, refactor, style, or test.
```
