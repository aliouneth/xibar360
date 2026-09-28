<?php

namespace App\Services;

class SummarizerService
{
    public function summarize(string $content): string
    {
        $sentences = preg_split('/(?<=[.?!])\s+/', trim($content));
        $sentences = array_filter($sentences, fn($s) => strlen(trim($s)) > 10);
        $sentences = array_values($sentences);

        if (empty($sentences)) {
            return '';
        }

        if (count($sentences) <= 3) {
            return implode(' ', $sentences);
        }

        $scored = [];
        foreach ($sentences as $index => $sentence) {
            $scored[] = ['sentence' => $sentence, 'score' => $this->scoreSentence($sentence, $index, count($sentences))];
        }

        usort($scored, fn($a, $b) => $b['score'] <=> $a['score']);
        $top = array_slice($scored, 0, 3);

        usort($top, function ($a, $b) use ($sentences) {
            return array_search($a['sentence'], $sentences) <=> array_search($b['sentence'], $sentences);
        });

        return implode(' ', array_column($top, 'sentence'));
    }

    public function summarizeMultiple(array $contents): array
    {
        $results = [];
        foreach ($contents as $key => $content) {
            $results[$key] = $this->summarize($content);
        }
        return $results;
    }

    private function scoreSentence(string $sentence, int $index, int $total): float
    {
        $score = 0.0;

        $positionWeight = 1.0 - ($index / max($total, 1));
        $score += $positionWeight * 0.4;

        $firstWords = ['le', 'la', 'les', 'un', 'une', 'des', 'au', 'aux', 'est', 'sont', 'du', 'de', 'en', 'pour', 'que', 'qui', 'sur', 'avec', 'ce', 'cette'];
        $words = preg_split('/\s+/', strtolower($sentence));
        $keywordCount = count(array_intersect($words, $firstWords));
        $score += ($keywordCount / max(count($words), 1)) * 0.3;

        $lengthScore = min(strlen($sentence) / 200, 1.0) * 0.15;
        $score += $lengthScore;

        if ($index === 0) {
            $score += 0.15;
        }

        return $score;
    }
}
