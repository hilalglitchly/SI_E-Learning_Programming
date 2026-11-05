<?php

if (!function_exists('sanitize_rich_text')) {
    function sanitize_rich_text($html)
    {
        if (empty($html)) {
            return '';
        }

        $allowed_tags = '<p><br><b><strong><i><em><u><s><strike><blockquote><pre><code><ol><ul><li><h1><h2><h3><h4><h5><h6><a><img><span><div><sub><sup>';
        $clean = strip_tags($html, $allowed_tags);

        $dom = new \DOMDocument();
        libxml_use_internal_errors(true);
        $dom->loadHTML('<?xml encoding="utf-8" ?>' . $clean, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        libxml_clear_errors();

        $xpath = new \DOMXPath($dom);
        $nodes = $xpath->query('//*');

        $allowed_attrs = ['href', 'src', 'alt', 'class', 'target', 'rel', 'width', 'height'];

        foreach ($nodes as $node) {
            if ($node->hasAttributes()) {
                $attrs_to_remove = [];
                foreach ($node->attributes as $attr) {
                    $attr_name = strtolower($attr->nodeName);
                    $attr_val = strtolower(trim($attr->nodeValue));

                    if (!in_array($attr_name, $allowed_attrs)) {
                        $attrs_to_remove[] = $attr->nodeName;
                    } elseif (in_array($attr_name, ['href', 'src'])) {
                        if (preg_match('/^(javascript|data|vbscript):/i', $attr_val)) {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    }
                }
                foreach ($attrs_to_remove as $attr_name) {
                    $node->removeAttribute($attr_name);
                }
            }
        }

        $result = $dom->saveHTML();
        return str_replace('<?xml encoding="utf-8" ?>', '', $result);
    }
}

if (!function_exists('timeAgo')) {
    function timeAgo($datetime)
    {
        if (empty($datetime)) {
            return 'baru saja';
        }
        try {
            $tz = new \DateTimeZone(config('app.timezone', 'Asia/Jakarta'));
            $now = new \DateTime('now', $tz);
            $ago = $datetime instanceof \DateTimeInterface ? $datetime : new \DateTime((string) $datetime, $tz);
            
            // Jika tanggal di masa depan (karena selisih detik/clock drift), anggap baru saja
            if ($ago > $now) {
                return 'baru saja';
            }

            $diff = $now->diff($ago);

            if ($diff->y > 0) return $diff->y . ' tahun lalu';
            if ($diff->m > 0) return $diff->m . ' bulan lalu';
            if ($diff->d > 0) return $diff->d . ' hari lalu';
            if ($diff->h > 0) return $diff->h . ' jam lalu';
            if ($diff->i > 0) return $diff->i . ' menit lalu';
            return 'baru saja';
        } catch (\Exception $e) {
            return 'baru saja';
        }
    }
}

if (!function_exists('getInitial')) {
    function getInitial($username)
    {
        return strtoupper(substr((string) $username, 0, 2));
    }
}

if (!function_exists('getTextPreview')) {
    function getTextPreview($html, $maxLen = 200)
    {
        $text = strip_tags((string) $html);
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        return mb_strlen($text) > $maxLen ? mb_substr($text, 0, $maxLen) . '...' : $text;
    }
}

if (!function_exists('extractCodeBlock')) {
    function extractCodeBlock($html)
    {
        if (preg_match('/<pre><code(?:\s+class="language-(\w+)")?>(.*?)<\/code><\/pre>/s', (string) $html, $matches)) {
            return [
                'lang' => $matches[1] ?? 'code',
                'code' => html_entity_decode($matches[2], ENT_QUOTES, 'UTF-8')
            ];
        }
        return null;
    }
}

