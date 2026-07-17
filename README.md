# RequestDesk_Qa — Shared Q&A Library for Magento 2

A foundation module that stores reusable question-and-answer pairs **once** and
lets any entity attach to them. A single pair can appear on a blog post *and* a
product at the same time, so Q&A content lives in one place instead of being
duplicated per page.

This module is the shared base for [`RequestDesk_Blog`](https://github.com/brentwpeterson/requestdesk-magento-blog)
and [`RequestDesk_Aeo`](https://github.com/brentwpeterson/requestdesk-magento-aeo).
It depends only on Magento core, so it installs and runs on its own.

- **Package:** `requestdesk/magento-qa`
- **Version:** 0.1.0
- **Requires:** PHP ^8.1, Magento 2 (`magento/framework`, `magento/module-store`)

---

## Why this exists

FAQ content is usually stored inline per page. The same answer ("How do I care
for a duffle bag?") then gets copy-pasted onto a blog post, a category page, and
three product pages, and drifts out of sync. This module stores each pair once
and links it to as many entities as you like, so one edit updates everywhere and
the FAQ schema stays consistent across the site.

## Data model

| Table | Purpose |
|-------|---------|
| `requestdesk_qa_pair` | The reusable pair: `qa_id`, `question`, `answer`, timestamps |
| `requestdesk_qa_link` | Polymorphic link: `qa_id` + `entity_type` + `entity_id` (+ `position`) |

The link table is polymorphic on purpose: `entity_type` is a short string
(`blog_post`, `product`, …) so new entity kinds can attach without a schema
change. Deleting a pair cascades its links (`ON DELETE CASCADE`).

## Admin UI

**Content → Q&A Library → Q&A Pairs**

A grid to create, edit, and delete pairs. The library is shared, so pairs
created here are available to attach from any consuming module (for example the
blog post form's "Q&A Pairs" multiselect, or a product's AEO FAQ).

ACL: `RequestDesk_Qa::pairs`.

## Developer API

Consuming modules integrate through two services.

### `QaLinkResolver`

```php
use RequestDesk\Qa\Model\QaLinkResolver;

// Entity-type constants
QaLinkResolver::ENTITY_BLOG_POST; // 'blog_post'
QaLinkResolver::ENTITY_PRODUCT;   // 'product'

// Read the pairs attached to an entity (ordered by position)
$pairs = $resolver->getPairsFor(QaLinkResolver::ENTITY_BLOG_POST, $postId);
// [['qa_id' => 1, 'question' => '...', 'answer' => '...'], ...]

// Just the ids (e.g. to preselect a form multiselect)
$ids = $resolver->getQaIdsFor(QaLinkResolver::ENTITY_PRODUCT, $productId);

// Attach / detach a single pair
$resolver->attach($qaId, QaLinkResolver::ENTITY_PRODUCT, $productId, $position = 0);
$resolver->detach($qaId, QaLinkResolver::ENTITY_PRODUCT, $productId);

// Replace an entity's whole set (add + remove to match exactly)
$resolver->syncForEntity(QaLinkResolver::ENTITY_BLOG_POST, $postId, [1, 4, 7]);
```

`syncForEntity()` is what a save controller calls: pass the full list of ids the
entity should have and it reconciles adds and removals. Detaching a pair never
deletes the pair itself, only the link.

### `FaqSchemaBuilder`

```php
use RequestDesk\Qa\Model\FaqSchemaBuilder;

// Turn pairs into a schema.org FAQPage node (JSON-LD ready)
$node = $builder->build($pairs, $withContext = true);
// null when $pairs is empty
```

Pass the pairs from `getPairsFor()` to emit a `FAQPage` block. Use
`$withContext = false` when embedding the node inside a larger `@graph`.

## Installation

### Composer (recommended)

```bash
composer require requestdesk/magento-qa
bin/magento module:enable RequestDesk_Qa
bin/magento setup:upgrade
bin/magento setup:di:compile
bin/magento cache:flush
```

### Manual

Copy this directory to `app/code/RequestDesk/Qa`, then run the same
`module:enable` / `setup:upgrade` / `di:compile` / `cache:flush` sequence.

### Verify

```bash
bin/magento module:status RequestDesk_Qa   # Module is enabled
```

## Using it from your own module

1. Add `requestdesk/magento-qa` to your `composer.json` `require`.
2. Add `<module name="RequestDesk_Qa"/>` to your `etc/module.xml` `<sequence>`.
3. Inject `QaLinkResolver` (and `FaqSchemaBuilder` if you emit schema) and
   attach/read pairs against your own `entity_type` string.

## License

OSL-3.0
