<?php
/**
 * Collapse whitespace + synonym map so similar queries share cache and match intents.
 * COMPARE targets must be parsed from chatbot_collapse_message() BEFORE canonicalize.
 */

function chatbot_collapse_message(string $message): string
{
    $s = mb_strtolower(trim($message), 'UTF-8');
    $s = trim(preg_replace('/\s+/u', ' ', $s));
    return $s;
}

/**
 * Apply synonym replacements for cache key + non-compare intent detection.
 */
function chatbot_canonicalize_message(string $collapsedMessage): string
{
    $s = $collapsedMessage;

    $phrases = [
        '/\bleast\s+expensive\b/u' => 'cheapest',
        '/\bminimum\s+price\b/u'  => 'cheap',
        '/\blowest\s+price\b/u'   => 'cheapest',
        '/\blow\s+price\b/u'      => 'cheap',
        '/\bhigh\s+end\b/u'       => 'expensive',
        '/\btop\s+end\b/u'        => 'expensive',
        '/\bhighest\s+price\b/u' => 'expensive',
        '/\bmost\s+expensive\b/u' => 'expensive',
        '/\bpriciest\b/u'         => 'expensive',
    ];
    foreach ($phrases as $pattern => $replacement) {
        $s = preg_replace($pattern, $replacement, $s);
    }

    $wordMap = [
        'budget'      => 'cheap',
        'affordable'  => 'cheap',
        'bargain'     => 'cheap',
        'economical'  => 'cheap',
        'inexpensive' => 'cheap',
        'luxury'      => 'expensive',
        'premium'     => 'expensive',
        'costly'      => 'expensive',
        'upscale'     => 'expensive',
    ];
    foreach ($wordMap as $from => $to) {
        $s = preg_replace('/\b' . preg_quote($from, '/') . '\b/u', $to, $s);
    }

    return trim(preg_replace('/\s+/u', ' ', $s));
}
