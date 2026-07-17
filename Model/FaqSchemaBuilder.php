<?php
/**
 * RequestDesk Q&A - FAQPage Schema Builder (shared)
 *
 * @category  RequestDesk
 * @package   RequestDesk_Qa
 */

declare(strict_types=1);

namespace RequestDesk\Qa\Model;

/**
 * Builds a schema.org FAQPage node from Q&A pairs. Shared so every consumer
 * (blog posts, product pages) emits identical FAQ structured data.
 */
class FaqSchemaBuilder
{
    /**
     * Build the FAQPage node, or null when there are no usable pairs.
     *
     * @param array<int, array{question:string, answer:string}> $pairs
     * @param bool $withContext Include @context (true for a standalone node,
     *                          false when nesting inside an @graph)
     * @return array<string, mixed>|null
     */
    public function build(array $pairs, bool $withContext = true): ?array
    {
        $entities = [];
        foreach ($pairs as $pair) {
            $question = trim((string) ($pair['question'] ?? ''));
            $answer = trim((string) ($pair['answer'] ?? ''));
            if ($question === '' || $answer === '') {
                continue;
            }
            $entities[] = [
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $answer],
            ];
        }

        if ($entities === []) {
            return null;
        }

        $node = ['@type' => 'FAQPage', 'mainEntity' => $entities];
        if ($withContext) {
            $node = ['@context' => 'https://schema.org'] + $node;
        }
        return $node;
    }
}
