<?php
/**
 * Keyword-based intent on canonicalized message (no AI). COMPARE is resolved in chat.php from collapsed text.
 */

const CHATBOT_INTENT_CHEAPEST       = 'CHEAPEST';
const CHATBOT_INTENT_MOST_EXPENSIVE = 'MOST_EXPENSIVE';
const CHATBOT_INTENT_FILTER         = 'FILTER';
const CHATBOT_INTENT_COMPARE        = 'COMPARE';
const CHATBOT_INTENT_RECOMMENDATION = 'RECOMMENDATION';
const CHATBOT_INTENT_GENERAL        = 'GENERAL';

/**
 * Live Gemini required: RECOMMENDATION/GENERAL, or open-ended / planning keywords.
 * COMPARE is excluded by caller — keep structured compare on DB.
 */
function chatbot_requires_live_ai(string $collapsedMessage, string $canonicalMessage, string $intent): bool
{
    if ($intent === CHATBOT_INTENT_COMPARE) {
        return false;
    }
    if ($intent === CHATBOT_INTENT_RECOMMENDATION || $intent === CHATBOT_INTENT_GENERAL) {
        return true;
    }
    $hay = $collapsedMessage . ' ' . $canonicalMessage;
    if (preg_match('/\b(plan|planning|itinerary|suggest|unique|romantic)\b/i', $hay)) {
        return true;
    }
    return false;
}

/**
 * @return array{intent: string, filter_location?: string}
 */
function detectIntentFromCanonical(string $canonicalMessage): array
{
    $msg = $canonicalMessage;

    if (preg_match('/\b(cheap|cheapest|lowest|affordable|minimum\s+price)\b/u', $msg)) {
        return ['intent' => CHATBOT_INTENT_CHEAPEST];
    }

    if (preg_match('/\b(expensive|costly|luxury|premium|highest\s+price|top\s+end)\b/u', $msg)) {
        return ['intent' => CHATBOT_INTENT_MOST_EXPENSIVE];
    }

    $loc = extract_filter_location($msg);
    if ($loc !== null && $loc !== '') {
        return [
            'intent'          => CHATBOT_INTENT_FILTER,
            'filter_location' => $loc,
        ];
    }

    if (preg_match('/\b(recommend|recommendation|suggestion|suggest|best\s+package|which\s+package|what\s+should\s+i|good\s+for|ideal\s+for|honeymoon|family\s+trip|solo|romantic|unique|plan|planning|itinerary)\b/i', $msg)) {
        return ['intent' => CHATBOT_INTENT_RECOMMENDATION];
    }

    return ['intent' => CHATBOT_INTENT_GENERAL];
}

/**
 * @return array{0: string, 1: string}|null
 */
function extract_compare_targets(string $msg): ?array
{
    $clean = preg_replace('/\b(please|can\s+you|could\s+you|i\s+want\s+to|help\s+me)\b/i', '', $msg);
    $clean = trim(preg_replace('/\s+/', ' ', $clean));

    if (preg_match('/\bcompare\s+(.+?)\s+(?:with|and|to|&)\s+(.+)$/i', $clean, $m)) {
        return [trim_compare_phrase($m[1]), trim_compare_phrase($m[2])];
    }
    if (preg_match('/\bcompare\s+(.+)$/i', $clean, $m) && preg_match('/\s+(?:and|with|to|&)\s+/i', $m[1], $mm, PREG_OFFSET_CAPTURE)) {
        $pos = $mm[0][1];
        $a = trim(substr($m[1], 0, $pos));
        $b = trim(substr($m[1], $pos + strlen($mm[0][0])));
        if ($a !== '' && $b !== '') {
            return [trim_compare_phrase($a), trim_compare_phrase($b)];
        }
    }
    if (preg_match('/^(.+?)\s+(?:vs\.?|versus)\s+(.+)$/i', $clean, $m)) {
        return [trim_compare_phrase($m[1]), trim_compare_phrase($m[2])];
    }

    return null;
}

function trim_compare_phrase(string $s): string
{
    $s = trim($s, " \t\n\r\0\x0B.,!?\"'");
    $s = preg_replace('/^(the|a|an)\s+/i', '', $s);
    return trim($s);
}

function extract_filter_location(string $msg): ?string
{
    if (!preg_match('/\b(package|packages|tour|tours|trip|trips|destination|travel|traveling|holiday|holidays|vacation|getaway)\b/i', $msg)) {
        return null;
    }

    if (preg_match('/\b(?:in|to|at|near|around)\s+([\p{L}][\p{L}\s\-\']{1,48})(?:\?|$|\.|,)/u', $msg, $m)) {
        return trim($m[1]);
    }
    if (preg_match('/\b(?:in|to|at|near)\s+([\p{L}][\p{L}\s\-\']{1,48})\b/u', $msg, $m)) {
        return trim($m[1]);
    }

    return null;
}

/**
 * Short / vague user text → suggest follow-up in AI prompt.
 */
function chatbot_is_vague_query(string $canonicalMessage): bool
{
    $t = trim($canonicalMessage);
    if ($t === '') {
        return true;
    }
    $words = preg_split('/\s+/u', $t, -1, PREG_SPLIT_NO_EMPTY) ?: [];
    $wordCount = count($words);
    if (mb_strlen($t, 'UTF-8') <= 18 && $wordCount <= 3) {
        return true;
    }
    if ($wordCount <= 2 && !preg_match('/\b(package|trip|tour|travel|book|price|destination)\b/i', $t)) {
        return true;
    }
    return false;
}
