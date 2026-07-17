<?php
/**
 * Copyright (c) 2026 Content Basis LLC
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available at https://opensource.org/licenses/OSL-3.0
 *
 * RequestDesk Q&A Library for Magento 2
 * Shared, reusable question-and-answer pairs that any entity (blog posts,
 * products, ...) can attach to. Foundation module for RequestDesk_Blog and
 * RequestDesk_Aeo so Q&A content lives once and is reused, not duplicated.
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 * @author    Content Basis LLC
 * @copyright Copyright (c) 2026 Content Basis LLC
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License 3.0
 */

declare(strict_types=1);

use Magento\Framework\Component\ComponentRegistrar;

ComponentRegistrar::register(
    ComponentRegistrar::MODULE,
    'RequestDesk_Qa',
    __DIR__
);
