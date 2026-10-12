<?php

namespace App\Application\Portal;

use App\Domain\Portal\PortalPage;

final class DescribePlaceholderPage
{
    /** @return array{id:string,titleKey:string,descriptionKey:string} */
    public function execute(PortalPage $page): array
    {
        $prefix = 'portal.pages.'.$page->value;

        return [
            'id' => $page->value,
            'titleKey' => $prefix.'.title',
            'descriptionKey' => $prefix.'.description',
        ];
    }
}
