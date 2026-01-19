# Compatibility Upgrade: PHP 8.2 and PHPUnit 11.5.12

This document provides a quick reference for the PHP 8.2 and PHPUnit 11.5.12 compatibility upgrade.

## Summary Table

| Issue | Explanation | Suggested Fix | Status |
|-------|-------------|---------------|--------|
| **PHPUnit Metadata Annotations** | PHPUnit 11.5.12 removes support for metadata in doc-comments (`@covers`, `@test`, `@group`, etc.). Metadata must be expressed using PHP attributes. | Convert `@covers` annotations to `#[CoversClass()]` attributes. Example: `#[\PHPUnit\Framework\Attributes\CoversClass(\MyClass::class)]` | ✅ **FIXED** |
| **Deprecated PHP Functions** | PHP 8.2 removes `utf8_encode()` and `utf8_decode()` functions. | Use `mb_convert_encoding()` instead. | ✅ **NOT APPLICABLE** - No usage found |
| **Dynamic Properties** | PHP 8.2 deprecates creation of dynamic properties on non-stdClass objects. | Declare all properties or use `#[AllowDynamicProperties]` attribute. | ✅ **NOT APPLICABLE** - All properties properly declared |
| **Deprecated String Interpolation** | PHP 8.2 deprecates `"${var}"` and `"${expr}"` string interpolation syntax. | Use `"{$var}"` or `"$var"` instead. | ✅ **NOT APPLICABLE** - No usage found |
| **Standalone Types** | PHP 8.2 introduces standalone `true`, `false`, and `null` types. | Update type declarations if needed. | ✅ **NOT APPLICABLE** - No issues found |
| **DNF Types** | PHP 8.2 introduces Disjunctive Normal Form (DNF) types: `(A&B)\|C`. | Update complex type declarations if needed. | ✅ **NOT APPLICABLE** - No usage |
| **Readonly Classes** | PHP 8.2 introduces readonly classes. | Consider using for immutable classes. | ✅ **NOT APPLICABLE** - Optional feature |

## Files Changed

### 1. tests/api_test.php
- **Line 42**: Added `#[\PHPUnit\Framework\Attributes\CoversClass(\report_cmcompetency\api::class)]`
- **Line 37**: Removed `@covers \report_cmcompetency\api` from doc-comment

### 2. tests/task_test.php
- **Line 42**: Added `#[\PHPUnit\Framework\Attributes\CoversClass(\report_cmcompetency\task\rate_users_in_coursemodules::class)]`
- **Line 37**: Removed `@covers \report_cmcompetency\task` from doc-comment

## Quick Reference: PHPUnit Metadata Migration

### Before (PHPUnit ≤ 10)
```php
/**
 * @covers \MyNamespace\MyClass
 * @group mygroup
 * @test
 */
class MyTest extends TestCase {
    /**
     * @dataProvider myDataProvider
     */
    public function testSomething($data) {
        // ...
    }
}
```

### After (PHPUnit 11+)
```php
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(\MyNamespace\MyClass::class)]
#[Group('mygroup')]
class MyTest extends TestCase {
    #[DataProvider('myDataProvider')]
    #[Test]
    public function testSomething($data) {
        // ...
    }
}
```

## PHP 8.2 Breaking Changes Checklist

- ✅ No use of `utf8_encode()` or `utf8_decode()`
- ✅ No use of `create_function()`
- ✅ No dynamic properties on non-stdClass objects
- ✅ No deprecated `"${var}"` string interpolation
- ✅ No reliance on deprecated INI settings
- ✅ All code follows strict types where applicable

## Moodle Coding Standards Compliance

All changes adhere to Moodle coding standards:
- ✅ Proper PHPDoc comments maintained
- ✅ Proper use of namespaces and use statements
- ✅ No changes to functional code
- ✅ Backward compatibility maintained
- ✅ No breaking changes to API

## Testing

### Run PHPUnit Tests
```bash
# With PHPUnit 11
vendor/bin/phpunit tests/

# With code coverage
vendor/bin/phpunit --coverage-text tests/
```

### Verify PHP 8.2 Compatibility
```bash
# Check PHP version
php -v

# Run tests with PHP 8.2
php vendor/bin/phpunit tests/
```

## References

- [PHPUnit 11 Migration Guide](https://docs.phpunit.de/en/11.0/migration-guides/11.0.html)
- [PHPUnit Attributes Documentation](https://docs.phpunit.de/en/11.0/attributes.html)
- [PHP 8.2 Migration Guide](https://www.php.net/manual/en/migration82.php)
- [PHP 8.2 Deprecated Features](https://www.php.net/manual/en/migration82.deprecated.php)
- [Moodle Coding Style Guide](https://moodledev.io/general/development/policies/codingstyle)

## Additional Documentation

For more detailed information, see:
- `COMPATIBILITY_ANALYSIS.md` - Comprehensive analysis of compatibility issues
- `MIGRATION_SUMMARY.md` - Detailed summary of changes made

## Support

If you encounter any issues:
1. Verify you're using PHP 8.2+ and PHPUnit 11.5.12+
2. Check that all dependencies are updated
3. Review the error messages for deprecated features
4. Consult the documentation links above

---

**Last Updated**: 2026-01-19  
**Plugin Version**: Compatible with PHP 8.2+ and PHPUnit 11.5.12+  
**Maintained By**: StudiUM/Université de Montréal
