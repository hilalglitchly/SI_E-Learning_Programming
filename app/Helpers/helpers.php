<?php

if (!function_exists('sanitize_rich_text')) {
    function sanitize_rich_text($html)
    {
        if (empty($html)) {
            return '';
        }

        // shortcut: custom DOMDocument allowlist sanitizer; upgrade to ezyang/htmlpurifier if richer HTML/CSS is needed.
        $allowed_tags = '<p><br><b><strong><i><em><u><s><strike><blockquote><pre><code><ol><ul><li><h1><h2><h3><h4><h5><h6><a><img><span><div><sub><sup>';
        $clean = strip_tags((string) $html, $allowed_tags);

        $dom = new \DOMDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $dom->loadHTML(
            '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head><body>' . $clean . '</body></html>'
        );
        libxml_clear_errors();

        $body = $dom->getElementsByTagName('body')->item(0);
        if (!$body) {
            return '';
        }

        $tag_allowed_attrs = [
            'a' => ['href', 'target', 'rel', 'class'],
            'img' => ['src', 'alt', 'width', 'height', 'class'],
        ];
        $global_allowed_attrs = ['class'];

        $xpath = new \DOMXPath($dom);
        foreach ($xpath->query('.//*', $body) as $node) {
            if (!$node instanceof \DOMElement) {
                continue;
            }
            $tag = strtolower($node->tagName);
            $allowed = $tag_allowed_attrs[$tag] ?? $global_allowed_attrs;

            if ($node->hasAttributes()) {
                $attrs_to_remove = [];
                foreach ($node->attributes as $attr) {
                    $attr_name = strtolower($attr->nodeName);
                    $attr_raw = $attr->nodeValue;

                    if (!in_array($attr_name, $allowed, true)) {
                        $attrs_to_remove[] = $attr->nodeName;
                        continue;
                    }

                    if ($attr_name === 'href') {
                        $normalized = preg_replace('/[\x00-\x20\x7f]+/', '', html_entity_decode($attr_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        if (!preg_match('/^(https?:\/\/|mailto:|#)/i', $normalized)) {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    } elseif ($attr_name === 'src') {
                        $normalized = preg_replace('/[\x00-\x20\x7f]+/', '', html_entity_decode($attr_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8'));
                        if (!preg_match('/^https?:\/\//i', $normalized)) {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    } elseif ($attr_name === 'target') {
                        if (strtolower(trim($attr_raw)) !== '_blank') {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    } elseif ($attr_name === 'class') {
                        if (!preg_match('/^[a-zA-Z0-9_\-\s]+$/', $attr_raw)) {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    } elseif (in_array($attr_name, ['width', 'height'], true)) {
                        if (!preg_match('/^\d{1,4}$/', trim($attr_raw))) {
                            $attrs_to_remove[] = $attr->nodeName;
                        }
                    }
                }
                foreach ($attrs_to_remove as $attr_name) {
                    $node->removeAttribute($attr_name);
                }
            }

            if ($tag === 'a' && strtolower($node->getAttribute('target')) === '_blank') {
                $node->setAttribute('rel', 'noopener noreferrer');
            }
        }

        $result = '';
        foreach ($body->childNodes as $child) {
            $result .= $dom->saveHTML($child);
        }

        return $result;
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

if (!function_exists('extract_video_embed_url')) {
    function extract_video_embed_url(?string $url): ?string
    {
        $url = trim((string) $url);
        if ($url === '' || strlen($url) > 255) {
            return null;
        }

        if (preg_match('/^https:\/\/(?:www\.|m\.)?(?:youtube\.com\/(?:watch\?(?:[^#]*&)?v=|embed\/|shorts\/)|youtu\.be\/)([a-zA-Z0-9_-]{11})(?:[?&#].*)?$/i', $url, $m)) {
            return 'https://www.youtube-nocookie.com/embed/' . $m[1];
        }

        if (preg_match('/^https:\/\/(?:www\.)?(?:vimeo\.com\/|player\.vimeo\.com\/video\/)(\d{6,12})(?:[?&#].*)?$/i', $url, $m)) {
            return 'https://player.vimeo.com/video/' . $m[1];
        }

        return null;
    }
}

