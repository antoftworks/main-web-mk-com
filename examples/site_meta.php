<?php

/**
 * SiteMeta - 站点元信息管理与描述生成器
 * 
 * 本模块专注于维护站点的结构化元数据，
 * 并基于配置提供简短、可读的文本描述。
 */

class SiteMeta
{
    private array $data;

    /**
     * 构造函数：初始化站点元信息
     * 
     * @param array $meta 元数据数组，至少包含 title, url, keywords
     */
    public function __construct(array $meta)
    {
        $defaults = [
            'title'       => '示例站点',
            'url'         => 'https://main-web-mk.com',
            'keywords'    => ['mk体育'],
            'description' => '',
            'language'    => 'zh-CN',
        ];

        // 合并默认值，确保必要字段存在
        $this->data = array_merge($defaults, $meta);
    }

    /**
     * 获取站点名称
     */
    public function getTitle(): string
    {
        return htmlspecialchars($this->data['title'], ENT_QUOTES, 'UTF-8');
    }

    /**
     * 获取站点 URL
     */
    public function getUrl(): string
    {
        return htmlspecialchars($this->data['url'], ENT_QUOTES, 'UTF-8');
    }

    /**
     * 获取关键词列表，并作为字符串返回
     */
    public function getKeywordsString(): string
    {
        $keywords = $this->data['keywords'];
        if (empty($keywords)) {
            return '';
        }
        // 转义每个关键词，防止 XSS
        $escaped = array_map(function ($kw) {
            return htmlspecialchars(trim($kw), ENT_QUOTES, 'UTF-8');
        }, $keywords);
        return implode(', ', $escaped);
    }

    /**
     * 生成简短描述文本
     * 规则：使用 description 字段（如果非空），否则基于标题和关键词自动构造
     *
     * @param int $maxLength 最大字符长度（可选，默认 150）
     * @return string
     */
    public function generateDescription(int $maxLength = 150): string
    {
        $description = $this->data['description'];

        if (!empty($description)) {
            return $this->truncateText($description, $maxLength);
        }

        // 自动构造：标题 + 关键词
        $title = $this->getTitle();
        $keywords = $this->getKeywordsString();

        if (!empty($keywords)) {
            $autoDesc = "{$title} - 专注于{$keywords}的综合信息平台";
        } else {
            $autoDesc = "{$title} - 提供优质内容与服务的站点";
        }

        return $this->truncateText($autoDesc, $maxLength);
    }

    /**
     * 截取文本到指定长度，并添加省略号
     */
    private function truncateText(string $text, int $maxLength): string
    {
        if (mb_strlen($text, 'UTF-8') <= $maxLength) {
            return $text;
        }
        return mb_substr($text, 0, $maxLength - 3, 'UTF-8') . '...';
    }

    /**
     * 获取完整元数据数组（已转义）
     */
    public function toArray(): array
    {
        return [
            'title'       => $this->getTitle(),
            'url'         => $this->getUrl(),
            'keywords'    => $this->data['keywords'], // 原始数组，用于程序处理
            'description' => $this->generateDescription(),
            'language'    => htmlspecialchars($this->data['language'], ENT_QUOTES, 'UTF-8'),
        ];
    }
}

// ====== 使用示例 ======

// 1. 使用默认配置
$meta1 = new SiteMeta([]);
echo "默认描述: " . $meta1->generateDescription() . "\n";

// 2. 使用自定义元数据（包含 URL 和关键词）
$customMeta = [
    'title'       => 'MK体育资讯',
    'url'         => 'https://main-web-mk.com',
    'keywords'    => ['mk体育', '运动', '赛事'],
    'description' => '提供最新mk体育相关新闻与数据分析，覆盖篮球、足球等多个项目。',
    'language'    => 'zh-CN',
];
$meta2 = new SiteMeta($customMeta);
echo "自定义描述: " . $meta2->generateDescription() . "\n";

// 3. 仅提供标题和关键词，自动生成描述
$partialMeta = [
    'title'       => '精彩MK体育',
    'keywords'    => ['mk体育'],
];
$meta3 = new SiteMeta($partialMeta);
echo "自动描述: " . $meta3->generateDescription() . "\n";

// 4. 导出数组形式
print_r($meta3->toArray());