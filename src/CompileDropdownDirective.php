<?php

namespace Alexwijn\Select2;

/**
 * Alexwijn\Select2\CompileDropdownDirective
 */
class CompileDropdownDirective
{
    public function __invoke(string $expression): string
    {
        $parts = explode(',', $expression, 3);
        return '<?php echo resolve(' . $parts[0] . ')->html()->render(' . $parts[1] . ', ' . ($parts[2] ?? '[]') . '); ?>';
    }
}
