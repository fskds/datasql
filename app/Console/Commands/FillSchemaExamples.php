<?php

namespace App\Console\Commands;

use App\Models\Schema\SchemaType;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class FillSchemaExamples extends Command
{
    protected $signature = 'schema:fill-examples {--dry : 只预览不写入}';

    protected $description = '使用内置知识为 schema_types 填充/替换 example 字段（真实 JSON-LD 实例）';

    /**
     * 类型名 => 真实含义的 JSON-LD 实例（以字段值形式存储，不含缩进换行也可，此处保留可读性）
     */
    private function examples(): array
    {
        return [
            'Thing' => '{
  "@context": "https://schema.org",
  "@type": "Thing",
  "name": "罗浮宫",
  "description": "一座位于巴黎的著名艺术博物馆",
  "url": "https://www.louvre.fr/",
  "image": "https://www.louvre.fr/site-map.jpg"
}',
            'CreativeWork' => '{
  "@context": "https://schema.org",
  "@type": "CreativeWork",
  "name": "时间简史",
  "author": {
    "@type": "Person",
    "name": "史蒂芬·霍金"
  },
  "datePublished": "1988"
}',
            'Article' => '{
  "@context": "https://schema.org",
  "@type": "Article",
  "headline": "深度解析人工智能的未来趋势",
  "author": {
    "@type": "Person",
    "name": "张三"
  },
  "datePublished": "2024-06-01",
  "publisher": {
    "@type": "Organization",
    "name": "科技周刊"
  }
}',
            'NewsArticle' => '{
  "@context": "https://schema.org",
  "@type": "NewsArticle",
  "headline": "本地发现新出土文物",
  "datePublished": "2024-03-15T09:00:00+08:00",
  "author": {
    "@type": "Person",
    "name": "李四"
  },
  "publisher": {
    "@type": "Organization",
    "name": "日报社"
  }
}',
            'BlogPosting' => '{
  "@context": "https://schema.org",
  "@type": "BlogPosting",
  "headline": "我的环球旅行日记",
  "author": {
    "@type": "Person",
  "name": "小王"
  },
  "datePublished": "2024-02-20"
}',
            'VideoObject' => '{
  "@context": "https://schema.org",
  "@type": "VideoObject",
  "name": "如何做早餐",
  "uploadDate": "2024-05-10",
  "duration": "PT10M",
  "thumbnailUrl": "https://example.com/thumb.jpg"
}',
            'ImageObject' => '{
  "@context": "https://schema.org",
  "@type": "ImageObject",
  "contentUrl": "https://example.com/photo.jpg",
  "caption": "夕阳下的城市天际线",
  "width": 1920,
  "height": 1080
}',
            'AudioObject' => '{
  "@context": "https://schema.org",
  "@type": "AudioObject",
  "name": "播客：对话创业者",
  "contentUrl": "https://example.com/episode.mp3",
  "duration": "PT45M"
}',
            'Book' => '{
  "@context": "https://schema.org",
  "@type": "Book",
  "name": "百年孤独",
  "author": {
    "@type": "Person",
    "name": "加西亚·马尔克斯"
  },
  "numberOfPages": 360,
  "isbn": "9787503321720"
}',
            'Person' => '{
  "@context": "https://schema.org",
  "@type": "Person",
  "name": "李明",
  "jobTitle": "软件工程师",
  "telephone": "+86-138-0000-0000",
  "email": "mailto:liming@example.com",
  "url": "https://liming.example.com/"
}',
            'Organization' => '{
  "@context": "https://schema.org",
  "@type": "Organization",
  "name": "示例科技有限公司",
  "url": "https://www.example.com/",
  "logo": "https://www.example.com/logo.png",
  "contactPoint": {
    "@type": "ContactPoint",
    "telephone": "+86-10-12345678",
    "contactType": "customer service"
  }
}',
            'Corporation' => '{
  "@context": "https://schema.org",
  "@type": "Corporation",
  "name": "示例股份有限公司",
  "tickerSymbol": "EXAMPLE",
  "foundingDate": "2001-01-01"
}',
            'LocalBusiness' => '{
  "@context": "https://schema.org",
  "@type": "LocalBusiness",
  "name": "幸福面包房",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "中山路128号"
  },
  "openingHours": "Mo-Su 08:00-20:00"
}',
            'Restaurant' => '{
  "@context": "https://schema.org",
  "@type": "Restaurant",
  "name": "川味居",
  "servesCuisine": "川菜",
  "priceRange": "$$",
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "120"
  }
}',
            'Hotel' => '{
  "@context": "https://schema.org",
  "@type": "Hotel",
  "name": "海景大酒店",
  "starRating": {
    "@type": "Rating",
    "ratingValue": "5"
  },
  "address": {
    "@type": "PostalAddress",
    "addressLocality": "三亚"
  }
}',
            'Place' => '{
  "@context": "https://schema.org",
  "@type": "Place",
  "name": "西湖",
  "geo": {
    "@type": "GeoCoordinates",
    "latitude": 30.2590,
    "longitude": 120.1488
  }
}',
            'TouristAttraction' => '{
  "@context": "https://schema.org",
  "@type": "TouristAttraction",
  "name": "长城",
  "description": "世界文化遗产，绵延数千公里的古代防御工程",
  "isAccessibleForFree": true
}',
            'Park' => '{
  "@context": "https://schema.org",
  "@type": "Park",
  "name": "中央公园",
  "openingHours": "Mo-Su 06:00-22:00"
}',
            'Event' => '{
  "@context": "https://schema.org",
  "@type": "Event",
  "name": "2024年科技峰会",
  "startDate": "2024-09-01T09:00:00+08:00",
  "endDate": "2024-09-03T18:00:00+08:00",
  "location": {
    "@type": "Place",
    "name": "国家会议中心"
  }
}',
            'MusicEvent' => '{
  "@context": "https://schema.org",
  "@type": "MusicEvent",
  "name": "夏季音乐会",
  "startDate": "2024-07-20T19:30:00+08:00",
  "performer": { "@type": "Person", "name": "周杰" }
}',
            'Product' => '{
  "@context": "https://schema.org",
  "@type": "Product",
  "name": "无线蓝牙耳机 Pro",
  "brand": { "@type": "Brand", "name": "声动" },
  "offers": {
    "@type": "Offer",
    "price": "499.00",
    "priceCurrency": "CNY",
    "availability": "https://schema.org/InStock"
  }
}',
            'Offer' => '{
  "@context": "https://schema.org",
  "@type": "Offer",
  "price": "99.00",
  "priceCurrency": "CNY",
  "seller": { "@type": "Organization", "name": "示例商城" }
}',
            'AggregateRating' => '{
  "@context": "https://schema.org",
  "@type": "AggregateRating",
  "ratingValue": "4.6",
  "bestRating": "5",
  "ratingCount": "230"
}',
            'Review' => '{
  "@context": "https://schema.org",
  "@type": "Review",
  "reviewRating": { "@type": "Rating", "ratingValue": "4" },
  "reviewBody": "使用方便，做工精致。",
  "author": { "@type": "Person", "name": "用户甲" }
}',
            'Recipe' => '{
  "@context": "https://schema.org",
  "@type": "Recipe",
  "name": "红烧肉",
  "recipeIngredient": ["五花肉", "冰糖", "生抽", "老抽", "料酒"],
  "recipeInstructions": [{
    "@type": "HowToStep",
    "text": "五花肉切块焯水"
  }],
  "cookTime": "PT90M"
}',
            'JobPosting' => '{
  "@context": "https://schema.org",
  "@type": "JobPosting",
  "title": "前端开发工程师",
  "baseSalary": {
    "@type": "MonetaryAmount",
    "currency": "CNY",
    "value": { "@type": "QuantitativeValue", "value": 25000 }
  },
  "employmentType": "FULL_TIME"
}',
            'SoftwareApplication' => '{
  "@context": "https://schema.org",
  "@type": "SoftwareApplication",
  "name": "示例编辑器",
  "operatingSystem": "Windows, macOS",
  "applicationCategory": "DeveloperApplication",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "CNY" }
}',
            'WebPage' => '{
  "@context": "https://schema.org",
  "@type": "WebPage",
  "name": "产品介绍页",
  "url": "https://www.example.com/product",
  "inLanguage": "zh-CN"
}',
            'Movie' => '{
  "@context": "https://schema.org",
  "@type": "Movie",
  "name": "星际穿越",
  "director": { "@type": "Person", "name": "克里斯托弗·诺兰" },
  "datePublished": "2014",
  "duration": "PT2H49M"
}',
            'MusicAlbum' => '{
  "@context": "https://schema.org",
  "@type": "MusicAlbum",
  "name": "伟大的乐曲",
  "byArtist": { "@type": "MusicGroup", "name": "示例乐队" },
  "numTracks": 12
}',
            'TVSeries' => '{
  "@context": "https://schema.org",
  "@type": "TVSeries",
  "name": "权力的游戏",
  "numberOfSeasons": 8,
  "startDate": "2011-04-17"
}',
            'GovernmentOrganization' => '{
  "@context": "https://schema.org",
  "@type": "GovernmentOrganization",
  "name": "示例市政务服务局",
  "url": "https://gov.example.com/"
}',
            'NGO' => '{
  "@context": "https://schema.org",
  "@type": "NGO",
  "name": "绿野环保协会",
  "areaServed": "华东地区"
}',
            'EducationalOrganization' => '{
  "@context": "https://schema.org",
  "@type": "EducationalOrganization",
  "name": "示例大学",
  "url": "https://www.univ.example.com/"
}',
            'MedicalOrganization' => '{
  "@context": "https://schema.org",
  "@type": "MedicalOrganization",
  "name": "市中心人民医院",
  "medicalSpecialty": "https://schema.org/Cardiovascular"
}',
            'Hospital' => '{
  "@context": "https://schema.org",
  "@type": "Hospital",
  "name": "仁心医院",
  "address": {
    "@type": "PostalAddress",
    "streetAddress": "健康路1号"
  }
}',
            'PostalAddress' => '{
  "@context": "https://schema.org",
  "@type": "PostalAddress",
  "streetAddress": "中山路128号503室",
  "addressLocality": "上海市",
  "postalCode": "200000",
  "addressCountry": "CN"
}',
            'GeoCoordinates' => '{
  "@context": "https://schema.org",
  "@type": "GeoCoordinates",
  "latitude": 31.2304,
  "longitude": 121.4737
}',
            'ContactPoint' => '{
  "@context": "https://schema.org",
  "@type": "ContactPoint",
  "telephone": "+86-21-12345678",
  "contactType": "technical support",
  "availableLanguage": "zh-CN"
}',
            'Intangible' => '{
  "@context": "https://schema.org",
  "@type": "Intangible",
  "name": "无形资产的抽象概念"
}',
            'DateTime' => '{
  "@context": "https://schema.org",
  "@type": "DateTime",
  "name": "示例日期时间",
  "description": "ISO 8601 格式：2024-06-01T09:30:00+08:00"
}',
            'Boolean' => '{
  "@context": "https://schema.org",
  "@type": "Boolean",
  "name": "布尔值示例",
  "description": "true（真）或 false（假）"
}',
            'Date' => '{"@context":"https://schema.org","@type":"Date","name":"示例日期","description":"ISO 8601 格式：2024-06-01"}',
            'Number' => '{"@context":"https://schema.org","@type":"Number","name":"示例数值","description":"任意实数，如 3.14 或 42"}',
            'Text' => '{"@context":"https://schema.org","@type":"Text","name":"示例文本","description":"自由格式文本，如「你好，世界」"}',
            'URL' => '{"@context":"https://schema.org","@type":"URL","name":"示例链接","description":"https://www.example.com/page"}',
            'Time' => '{"@context":"https://schema.org","@type":"Time","name":"示例时间","description":"ISO 8601 时间：13:45:00+08:00"}',
            'Float' => '{"@context":"https://schema.org","@type":"Float","name":"示例浮点数","description":"带小数点的数值，如 2.71828"}',
            'Integer' => '{"@context":"https://schema.org","@type":"Integer","name":"示例整数","description":"不带小数点的数值，如 42"}',
            'Map' => '{
  "@context": "https://schema.org",
  "@type": "Map",
  "name": "城市旅行地图",
  "mapType": "https://schema.org/TransitMap"
}',
            'MediaObject' => '{
  "@context": "https://schema.org",
  "@type": "MediaObject",
  "contentUrl": "https://example.com/media/video.mp4",
  "encodingFormat": "video/mp4",
  "uploadDate": "2024-05-01"
}',
            'Painting' => '{
  "@context": "https://schema.org",
  "@type": "Painting",
  "name": "向日葵",
  "creator": { "@type": "Person", "name": "梵高" },
  "dateCreated": "1889"
}',
            'Photograph' => '{
  "@context": "https://schema.org",
  "@type": "Photograph",
  "name": "山谷日出",
  "image": "https://example.com/sunrise.jpg"
}',
            'Sculpture' => '{
  "@context": "https://schema.org",
  "@type": "Sculpture",
  "name": "思想者",
  "creator": { "@type": "Person", "name": "罗丹" }
}',
            'Blog' => '{
  "@context": "https://schema.org",
  "@type": "Blog",
  "name": "旅行笔记",
  "blogPost": { "@type": "BlogPosting", "headline": "第一次远行" }
}',
            'Comment' => '{
  "@context": "https://schema.org",
  "@type": "Comment",
  "text": "非常实用的文章，谢谢分享！",
  "author": { "@type": "Person", "name": "网友小李" },
  "dateCreated": "2024-05-10"
}',
            'MobileApplication' => '{
  "@context": "https://schema.org",
  "@type": "MobileApplication",
  "name": "记账助手",
  "operatingSystem": "Android, iOS",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "CNY" }
}',
            'WebApplication' => '{
  "@context": "https://schema.org",
  "@type": "WebApplication",
  "name": "在线文档编辑器",
  "browserRequirements": "Chrome, Firefox",
  "offers": { "@type": "Offer", "price": "0", "priceCurrency": "CNY" }
}',
            'TVEpisode' => '{
  "@context": "https://schema.org",
  "@type": "TVEpisode",
  "name": "第1集：重逢",
  "partOfSeason": { "@type": "TVSeason", "numberOfEpisodes": 8 },
  "episodeNumber": 1
}',
            'TVSeason' => '{
  "@context": "https://schema.org",
  "@type": "TVSeason",
  "name": "第一季",
  "numberOfEpisodes": 10,
  "startDate": "2023-01-01"
}',
            'Language' => '{
  "@context": "https://schema.org",
  "@type": "Language",
  "name": "Chinese",
  "alternateName": "中文"
}',
            'AggregateOffer' => '{
  "@context": "https://schema.org",
  "@type": "AggregateOffer",
  "lowPrice": "299.00",
  "highPrice": "899.00",
  "priceCurrency": "CNY",
  "offerCount": 12
}',
            'Rating' => '{
  "@context": "https://schema.org",
  "@type": "Rating",
  "ratingValue": "4.2",
  "bestRating": "5",
  "worstRating": "1"
}',
            'NutritionInformation' => '{
  "@context": "https://schema.org",
  "@type": "NutritionInformation",
  "servingSize": "100 克",
  "calories": "215 卡路里",
  "fatContent": "9 克",
  "proteinContent": "26 克"
}',
            'City' => '{
  "@context": "https://schema.org",
  "@type": "City",
  "name": "上海市",
  "url": "https://www.sh.gov.cn/"
}',
            'Country' => '{
  "@context": "https://schema.org",
  "@type": "Country",
  "name": "中国",
  "identifier": "CN"
}',
            'State' => '{
  "@context": "https://schema.org",
  "@type": "State",
  "name": "广东省",
  "addressCountry": "中国"
}',
            'Airport' => '{
  "@context": "https://schema.org",
  "@type": "Airport",
  "name": "上海浦东国际机场",
  "iataCode": "PVG",
  "address": { "@type": "PostalAddress", "addressLocality": "上海" }
}',
            'Museum' => '{
  "@context": "https://schema.org",
  "@type": "Museum",
  "name": "故宫博物院",
  "url": "https://www.dpm.org.cn/",
  "openingHours": "Tu-Su 08:30-17:00"
}',
            'Zoo' => '{
  "@context": "https://schema.org",
  "@type": "Zoo",
  "name": "市野生动物园",
  "openingHours": "Mo-Su 09:00-17:00"
}',
            'Beach' => '{
  "@context": "https://schema.org",
  "@type": "Beach",
  "name": "亚龙湾",
  "geo": { "@type": "GeoCoordinates", "latitude": 18.1990, "longitude": 109.6330 }
}',
            'Mountain' => '{
  "@context": "https://schema.org",
  "@type": "Mountain",
  "name": "泰山",
  "elevation": 1545
}',
            'LakeBodyOfWater' => '{
  "@context": "https://schema.org",
  "@type": "LakeBodyOfWater",
  "name": "西湖"
}',
            'OceanBodyOfWater' => '{"@context":"https://schema.org","@type":"OceanBodyOfWater","name":"太平洋"}',
            'RiverBodyOfWater' => '{"@context":"https://schema.org","@type":"RiverBodyOfWater","name":"长江"}',
            'SeaBodyOfWater' => '{"@context":"https://schema.org","@type":"SeaBodyOfWater","name":"东海"}',
            'Volcano' => '{"@context":"https://schema.org","@type":"Volcano","name":"富士山"}',
            'Continent' => '{"@context":"https://schema.org","@type":"Continent","name":"亚洲"}',
            'LodgingBusiness' => '{
  "@context": "https://schema.org",
  "@type": "LodgingBusiness",
  "name": "湖畔民宿",
  "priceRange": "300-600",
  "address": { "@type": "PostalAddress", "addressLocality": "杭州" }
}',
            'Physician' => '{
  "@context": "https://schema.org",
  "@type": "Physician",
  "name": "仁和医生诊所",
  "medicalSpecialty": "https://schema.org/PrimaryCare"
}',
            'Pharmacy' => '{
  "@context": "https://schema.org",
  "@type": "Pharmacy",
  "name": "康健大药房",
  "openingHours": "Mo-Su 08:00-22:00"
}',
            'Dentist' => '{
  "@context": "https://schema.org",
  "@type": "Dentist",
  "name": "微笑牙科诊所",
  "priceRange": "$$"
}',
            'Drug' => '{
  "@context": "https://schema.org",
  "@type": "Drug",
  "name": "阿司匹林",
  "drugClass": { "@type": "DrugClass", "name": "非甾体抗炎药" },
  "activeIngredient": "乙酰水杨酸"
}',
            'MedicalCondition' => '{
  "@context": "https://schema.org",
  "@type": "MedicalCondition",
  "name": "高血压",
  "associatedAnatomy": { "@type": "AnatomicalSystem", "name": "心血管系统" }
}',
            'MedicalEntity' => '{
  "@context": "https://schema.org",
  "@type": "MedicalEntity",
  "name": "示例医学实体",
  "code": { "@type": "MedicalCode", "codeValue": "I10", "codingSystem": "ICD-10" }
}',
            'ItemList' => '{
  "@context": "https://schema.org",
  "@type": "ItemList",
  "name": "本周销量前十",
  "numberOfItems": 10,
  "itemListElement": [
    { "@type": "ListItem", "position": 1, "name": "商品A" },
    { "@type": "ListItem", "position": 2, "name": "商品B" }
  ]
}',
            'Enumeration' => '{
  "@context": "https://schema.org",
  "@type": "Enumeration",
  "name": "示例枚举",
  "description": "枚举值通常作为 data property 使用，如商品可用性等"
}',
            'BookFormatType' => '{"@context":"https://schema.org","@type":"BookFormatType","name":"https://schema.org/Paperback","description":"平装本"}',
            'ItemAvailability' => '{"@context":"https://schema.org","@type":"ItemAvailability","name":"https://schema.org/InStock","description":"有货"}',
            'OfferItemCondition' => '{"@context":"https://schema.org","@type":"OfferItemCondition","name":"https://schema.org/NewCondition","description":"全新"}',
            // ---- 本地商家 / 餐饮 ----
            'Bakery' => '{"@context":"https://schema.org","@type":"Bakery","name":"麦香园面包坊","address":{"@type":"PostalAddress","streetAddress":"解放路1号"}}',
            'BarOrPub' => '{"@context":"https://schema.org","@type":"BarOrPub","name":"夜色酒吧","openingHours":"Mo-Su 19:00-02:00"}',
            'Brewery' => '{"@context":"https://schema.org","@type":"Brewery","name":"精酿工坊","address":{"@type":"PostalAddress","streetAddress":"创新大道88号"}}',
            'CafeOrCoffeeShop' => '{"@context":"https://schema.org","@type":"CafeOrCoffeeShop","name":"晨光咖啡","openingHours":"Mo-Fr 08:00-18:00"}',
            'FastFoodRestaurant' => '{"@context":"https://schema.org","@type":"FastFoodRestaurant","name":"汉堡快餐厅","servesCuisine":"西式快餐"}',
            'IceCreamShop' => '{"@context":"https://schema.org","@type":"IceCreamShop","name":"甜之夏冰淇淋","openingHours":"Mo-Su 10:00-22:00"}',
            'Winery' => '{"@context":"https://schema.org","@type":"Winery","name":"山谷酒庄","address":{"@type":"PostalAddress","addressLocality":"波尔多"}}',
            // ---- 汽车 / 服务 ----
            'AutomotiveBusiness' => '{"@context":"https://schema.org","@type":"AutomotiveBusiness","name":"安心汽修","openingHours":"Mo-Sa 08:00-18:00"}',
            'AutoBodyShop' => '{"@context":"https://schema.org","@type":"AutoBodyShop","name":"靓车钣金喷漆"}',
            'AutoDealer' => '{"@context":"https://schema.org","@type":"AutoDealer","name":"华通汽车4S店","telephone":"+86-20-12345678"}',
            'AutoPartsStore' => '{"@context":"https://schema.org","@type":"AutoPartsStore","name":"德系汽配"}',
            'AutoRental' => '{"@context":"https://schema.org","@type":"AutoRental","name":"快捷租车","priceRange":"$$"}',
            'AutoRepair' => '{"@context":"https://schema.org","@type":"AutoRepair","name":"迅达汽车维修"}',
            'AutoWash' => '{"@context":"https://schema.org","@type":"AutoWash","name":"洁净洗车坊"}',
            'GasStation' => '{"@context":"https://schema.org","@type":"GasStation","name":"中石化加油站","address":{"@type":"PostalAddress","streetAddress":"外环路66号"}}',
            'MotorcycleDealer' => '{"@context":"https://schema.org","@type":"MotorcycleDealer","name":"骑士摩托车行"}',
            'MotorcycleRepair' => '{"@context":"https://schema.org","@type":"MotorcycleRepair","name":"疾风摩托修理"}',
            'Electrician' => '{"@context":"https://schema.org","@type":"Electrician","name":"小明水电服务","telephone":"+86-130-0000-0000"}',
            'GeneralContractor' => '{"@context":"https://schema.org","@type":"GeneralContractor","name":"诚信建筑总承包"}',
            'HVACBusiness' => '{"@context":"https://schema.org","@type":"HVACBusiness","name":"冷暖空调工程"}',
            'HousePainter' => '{"@context":"https://schema.org","@type":"HousePainter","name":"色彩涂料施工"}',
            'Locksmith' => '{"@context":"https://schema.org","@type":"Locksmith","name":"开锁换锁24小时服务"}',
            'MovingCompany' => '{"@context":"https://schema.org","@type":"MovingCompany","name":"易搬家服务"}',
            'Plumber' => '{"@context":"https://schema.org","@type":"Plumber","name":"畅通管道维修"}',
            'RoofingContractor' => '{"@context":"https://schema.org","@type":"RoofingContractor","name":"防水屋顶工程"}',
            // ---- 美容健康 ----
            'BeautySalon' => '{"@context":"https://schema.org","@type":"BeautySalon","name":"焕颜美容院","priceRange":"$$$$"}',
            'DaySpa' => '{"@context":"https://schema.org","@type":"DaySpa","name":"静心日间水疗"}',
            'HairSalon' => '{"@context":"https://schema.org","@type":"HairSalon","name":"时尚造型沙龙"}',
            'HealthClub' => '{"@context":"https://schema.org","@type":"HealthClub","name":"活力健身俱乐部","openingHours":"Mo-Su 06:00-23:00"}',
            'NailSalon' => '{"@context":"https://schema.org","@type":"NailSalon","name":"纤指美甲"}',
            'TattooParlor' => '{"@context":"https://schema.org","@type":"TattooParlor","name":"墨韵纹身"}',
            // ---- 商店 ----
            'Store' => '{"@context":"https://schema.org","@type":"Store","name":"惠民日用品店","openingHours":"Mo-Su 08:00-22:00"}',
            'BikeStore' => '{"@context":"https://schema.org","@type":"BikeStore","name":"骑行天下自行车行"}',
            'BookStore' => '{"@context":"https://schema.org","@type":"BookStore","name":"思庐书店"}',
            'ClothingStore' => '{"@context":"https://schema.org","@type":"ClothingStore","name":"风尚时装店"}',
            'ComputerStore' => '{"@context":"https://schema.org","@type":"ComputerStore","name":"极客电脑城"}',
            'ConvenienceStore' => '{"@context":"https://schema.org","@type":"ConvenienceStore","name":"便利蜂旗舰店"}',
            'DepartmentStore' => '{"@context":"https://schema.org","@type":"DepartmentStore","name":"春天百货"}',
            'ElectronicsStore' => '{"@context":"https://schema.org","@type":"ElectronicsStore","name":"数码先锋"}',
            'Florist' => '{"@context":"https://schema.org","@type":"Florist","name":"花间集花店"}',
            'FurnitureStore' => '{"@context":"https://schema.org","@type":"FurnitureStore","name":"宜居家私"}',
            'GardenStore' => '{"@context":"https://schema.org","@type":"GardenStore","name":"绿意园艺"}',
            'GroceryStore' => '{"@context":"https://schema.org","@type":"GroceryStore","name":"鲜丰果蔬店"}',
            'HardwareStore' => '{"@context":"https://schema.org","@type":"HardwareStore","name":"五金大全"}',
            'HobbyShop' => '{"@context":"https://schema.org","@type":"HobbyShop","name":"模型爱好社"}',
            'HomeGoodsStore' => '{"@context":"https://schema.org","@type":"HomeGoodsStore","name":"优家家居用品"}',
            'JewelryStore' => '{"@context":"https://schema.org","@type":"JewelryStore","name":"盛世珠宝"}',
            'LiquorStore' => '{"@context":"https://schema.org","@type":"LiquorStore","name":"陈年酒坊"}',
            'MensClothingStore' => '{"@context":"https://schema.org","@type":"MensClothingStore","name":"绅士男装"}',
            'MobilePhoneStore' => '{"@context":"https://schema.org","@type":"MobilePhoneStore","name":"智选手机专卖"}',
            'MovieRentalStore' => '{"@context":"https://schema.org","@type":"MovieRentalStore","name":"光影影碟出租"}',
            'MusicStore' => '{"@context":"https://schema.org","@type":"MusicStore","name":"乐韵唱片行"}',
            'OfficeEquipmentStore' => '{"@context":"https://schema.org","@type":"OfficeEquipmentStore","name":"恒信办公设备"}',
            'OutletStore' => '{"@context":"https://schema.org","@type":"OutletStore","name":"品牌折扣廊"}',
            'PawnShop' => '{"@context":"https://schema.org","@type":"PawnShop","name":"典当老行"}',
            'PetStore' => '{"@context":"https://schema.org","@type":"PetStore","name":"宠物之家"}',
            'ShoeStore' => '{"@context":"https://schema.org","@type":"ShoeStore","name":"步步高鞋店"}',
            'SportingGoodsStore' => '{"@context":"https://schema.org","@type":"SportingGoodsStore","name":"运动达人用品店"}',
            'TireShop' => '{"@context":"https://schema.org","@type":"TireShop","name":"安心轮胎店"}',
            'ToyStore' => '{"@context":"https://schema.org","@type":"ToyStore","name":"童趣玩具城"}',
            'WholesaleStore' => '{"@context":"https://schema.org","@type":"WholesaleStore","name":"万物批发市场"}',
            // ---- 服务 / 机构 ----
            'AccountingService' => '{"@context":"https://schema.org","@type":"AccountingService","name":"正信会计服务"}',
            'AutomatedTeller' => '{"@context":"https://schema.org","@type":"AutomatedTeller","name":"工商银行ATM"}',
            'BankOrCreditUnion' => '{"@context":"https://schema.org","@type":"BankOrCreditUnion","name":"市商业银行"}',
            'InsuranceAgency' => '{"@context":"https://schema.org","@type":"InsuranceAgency","name":"平安保险代理"}',
            'EmploymentAgency' => '{"@context":"https://schema.org","@type":"EmploymentAgency","name":"前程职业介绍"}',
            'EmergencyService' => '{"@context":"https://schema.org","@type":"EmergencyService","name":"24小时急救服务"}',
            'FireStation' => '{"@context":"https://schema.org","@type":"FireStation","name":"市消防一站"}',
            'PoliceStation' => '{"@context":"https://schema.org","@type":"PoliceStation","name":"城东派出所"}',
            'PostOffice' => '{"@context":"https://schema.org","@type":"PostOffice","name":"中心邮政支局"}',
            'GovernmentOffice' => '{"@context":"https://schema.org","@type":"GovernmentOffice","name":"市民服务中心"}',
            'ChildCare' => '{"@context":"https://schema.org","@type":"ChildCare","name":"爱心托儿所"}',
            'RecyclingCenter' => '{"@context":"https://schema.org","@type":"RecyclingCenter","name":"绿色回收站"}',
            'SelfStorage' => '{"@context":"https://schema.org","@type":"SelfStorage","name":"储物仓连锁"}',
            'ShoppingCenter' => '{"@context":"https://schema.org","@type":"ShoppingCenter","name":"万象城购物中心"}',
            'InternetCafe' => '{"@context":"https://schema.org","@type":"InternetCafe","name":"极速网咖"}',
            'Library' => '{"@context":"https://schema.org","@type":"Library","name":"市公共图书馆"}',
            'TouristInformationCenter' => '{"@context":"https://schema.org","@type":"TouristInformationCenter","name":"景区游客服务中心"}',
            'TravelAgency' => '{"@context":"https://schema.org","@type":"TravelAgency","name":"环球旅行社"}',
            'AnimalShelter' => '{"@context":"https://schema.org","@type":"AnimalShelter","name":"流浪动物之家"}',
            // ---- 运动 / 娱乐 ----
            'AmusementPark' => '{"@context":"https://schema.org","@type":"AmusementPark","name":"欢乐世界游乐园"}',
            'ArtGallery' => '{"@context":"https://schema.org","@type":"ArtGallery","name":"当代艺术画廊"}',
            'MovieTheater' => '{"@context":"https://schema.org","@type":"MovieTheater","name":"星河影城"}',
            'NightClub' => '{"@context":"https://schema.org","@type":"NightClub","name":"迷城夜总会"}',
            'Casino' => '{"@context":"https://schema.org","@type":"Casino","name":"皇冠赌场"}',
            'ComedyClub' => '{"@context":"https://schema.org","@type":"ComedyClub","name":"爆笑喜剧俱乐部"}',
            'SportsActivityLocation' => '{"@context":"https://schema.org","@type":"SportsActivityLocation","name":"全民健身中心"}',
            'BowlingAlley' => '{"@context":"https://schema.org","@type":"BowlingAlley","name":"欢乐保龄球馆"}',
            'ExerciseGym' => '{"@context":"https://schema.org","@type":"ExerciseGym","name":"勤练健身房"}',
            'GolfCourse' => '{"@context":"https://schema.org","@type":"GolfCourse","name":"湖畔高尔夫球场"}',
            'PublicSwimmingPool' => '{"@context":"https://schema.org","@type":"PublicSwimmingPool","name":"市游泳馆"}',
            'SkiResort' => '{"@context":"https://schema.org","@type":"SkiResort","name":"雪山滑雪度假区"}',
            'SportsClub' => '{"@context":"https://schema.org","@type":"SportsClub","name":"铁人运动俱乐部"}',
            'StadiumOrArena' => '{"@context":"https://schema.org","@type":"StadiumOrArena","name":"市体育中心"}',
            'TennisComplex' => '{"@context":"https://schema.org","@type":"TennisComplex","name":"红土网球中心"}',
            // ---- 表演团体 ----
            'PerformingGroup' => '{"@context":"https://schema.org","@type":"PerformingGroup","name":"青年合唱团"}',
            'DanceGroup' => '{"@context":"https://schema.org","@type":"DanceGroup","name":"现代舞团"}',
            'MusicGroup' => '{"@context":"https://schema.org","@type":"MusicGroup","name":"蓝调乐队"}',
            'TheaterGroup' => '{"@context":"https://schema.org","@type":"TheaterGroup","name":"甬江话剧团"}',
            'SportsTeam' => '{"@context":"https://schema.org","@type":"SportsTeam","name":"东部篮球队","sport":"篮球"}',
            // ---- 医学 ----
            'MedicalEntity' => '{"@context":"https://schema.org","@type":"MedicalEntity","name":"示例医学实体","code":{"@type":"MedicalCode","codeValue":"I10","codingSystem":"ICD-10"}}',
            'BloodTest' => '{"@context":"https://schema.org","@type":"BloodTest","name":"血常规检查","normalRange":"血红蛋白 120-160 g/L"}',
            'ImagingTest' => '{"@context":"https://schema.org","@type":"ImagingTest","name":"胸部X光检查"}',
            'MedicalTestPanel' => '{"@context":"https://schema.org","@type":"MedicalTestPanel","name":"肝功能全套检查"}',
            'PathologyTest' => '{"@context":"https://schema.org","@type":"PathologyTest","name":"组织病理学检查"}',
            'MedicalTest' => '{"@context":"https://schema.org","@type":"MedicalTest","name":"血糖检测"}',
            'MedicalDevice' => '{"@context":"https://schema.org","@type":"MedicalDevice","name":"数字血压计"}',
            'MedicalTherapy' => '{"@context":"https://schema.org","@type":"MedicalTherapy","name":"药物治疗"}',
            'MedicalProcedure' => '{"@context":"https://schema.org","@type":"MedicalProcedure","name":"阑尾切除术","procedureType":"https://schema.org/SurgicalProcedure"}',
            'DiagnosticProcedure' => '{"@context":"https://schema.org","@type":"DiagnosticProcedure","name":"胃镜检查"}',
            'PalliativeProcedure' => '{"@context":"https://schema.org","@type":"PalliativeProcedure","name":"疼痛缓解治疗"}',
            'TherapeuticProcedure' => '{"@context":"https://schema.org","@type":"TherapeuticProcedure","name":"物理治疗"}',
            'InfectiousDisease' => '{"@context":"https://schema.org","@type":"InfectiousDisease","name":"流行性感冒","transmissionMethod":"呼吸道飞沫传播"}',
            'MedicalSignOrSymptom' => '{"@context":"https://schema.org","@type":"MedicalSignOrSymptom","name":"发热"}',
            'MedicalSign' => '{"@context":"https://schema.org","@type":"MedicalSign","name":"血压升高"}',
            'MedicalSymptom' => '{"@context":"https://schema.org","@type":"MedicalSymptom","name":"头痛"}',
            'MedicalIndication' => '{"@context":"https://schema.org","@type":"MedicalIndication","name":"降压适应症"}',
            'ApprovedIndication' => '{"@context":"https://schema.org","@type":"ApprovedIndication","name":"获批的2型糖尿病适应症"}',
            'PreventionIndication' => '{"@context":"https://schema.org","@type":"PreventionIndication","name":"预防流感的适应症"}',
            'TreatmentIndication' => '{"@context":"https://schema.org","@type":"TreatmentIndication","name":"治疗高血压的适应症"}',
            'MedicalRiskFactor' => '{"@context":"https://schema.org","@type":"MedicalRiskFactor","name":"吸烟","increasesRiskOf":["肺癌","冠心病"]}',
            'MedicalRiskEstimator' => '{"@context":"https://schema.org","@type":"MedicalRiskEstimator","name":"心血管风险评估工具"}',
            'MedicalRiskCalculator' => '{"@context":"https://schema.org","@type":"MedicalRiskCalculator","name":"卒中风险计算器"}',
            'MedicalRiskScore' => '{"@context":"https://schema.org","@type":"MedicalRiskScore","name":"查尔森合并症评分"}',
            'MedicalCause' => '{"@context":"https://schema.org","@type":"MedicalCause","name":"细菌感染"}',
            'MedicalContraindication' => '{"@context":"https://schema.org","@type":"MedicalContraindication","name":"对青霉素过敏"}',
            'MedicalGuideline' => '{"@context":"https://schema.org","@type":"MedicalGuideline","name":"高血压管理指南"}',
            'MedicalGuidelineContraindication' => '{"@context":"https://schema.org","@type":"MedicalGuidelineContraindication","name":"孕期禁用该药物指南"}',
            'MedicalGuidelineRecommendation' => '{"@context":"https://schema.org","@type":"MedicalGuidelineRecommendation","name":"推荐每日补充维生素D"}',
            'MedicalStudy' => '{"@context":"https://schema.org","@type":"MedicalStudy","name":"降压药疗效研究"}',
            'MedicalObservationalStudy' => '{"@context":"https://schema.org","@type":"MedicalObservationalStudy","name":"队列观察研究"}',
            'MedicalTrial' => '{"@context":"https://schema.org","@type":"MedicalTrial","name":"三期临床试验"}',
            'DrugClass' => '{"@context":"https://schema.org","@type":"DrugClass","name":"他汀类降脂药"}',
            'DrugStrength' => '{"@context":"https://schema.org","@type":"DrugStrength","name":"阿司匹林 100mg"}',
            'DrugCost' => '{"@context":"https://schema.org","@type":"DrugCost","name":"阿司匹林单位费用","costPerUnit":"0.5 元/片"}',
            'DrugLegalStatus' => '{"@context":"https://schema.org","@type":"DrugLegalStatus","name":"处方药"}',
            'DoseSchedule' => '{"@context":"https://schema.org","@type":"DoseSchedule","name":"每日一次给药方案"}',
            'MaximumDoseSchedule' => '{"@context":"https://schema.org","@type":"MaximumDoseSchedule","name":"最大剂量每日4000mg"}',
            'RecommendedDoseSchedule' => '{"@context":"https://schema.org","@type":"RecommendedDoseSchedule","name":"推荐剂量每日2000mg"}',
            'ReportedDoseSchedule' => '{"@context":"https://schema.org","@type":"ReportedDoseSchedule","name":"自报剂量每日1片"}',
            'DietarySupplement' => '{"@context":"https://schema.org","@type":"DietarySupplement","name":"复合维生素片","activeIngredient":"维生素C"}',
            'LifestyleModification' => '{"@context":"https://schema.org","@type":"LifestyleModification","name":"戒烟干预"}',
            'PhysicalActivity' => '{"@context":"https://schema.org","@type":"PhysicalActivity","name":"慢跑","category":"https://schema.org/AnaerobicActivity"}',
            'PhysicalTherapy' => '{"@context":"https://schema.org","@type":"PhysicalTherapy","name":"膝关节康复训练"}',
            'PsychologicalTreatment' => '{"@context":"https://schema.org","@type":"PsychologicalTreatment","name":"认知行为疗法"}',
            'RadiationTherapy' => '{"@context":"https://schema.org","@type":"RadiationTherapy","name":"肿瘤放射治疗"}',
            'SuperficialAnatomy' => '{"@context":"https://schema.org","@type":"SuperficialAnatomy","name":"上肢浅表解剖"}',
            'AnatomicalStructure' => '{"@context":"https://schema.org","@type":"AnatomicalStructure","name":"心脏"}',
            'AnatomicalSystem' => '{"@context":"https://schema.org","@type":"AnatomicalSystem","name":"神经系统"}',
            'Bone' => '{"@context":"https://schema.org","@type":"Bone","name":"股骨"}',
            'BrainStructure' => '{"@context":"https://schema.org","@type":"BrainStructure","name":"额叶"}',
            'Joint' => '{"@context":"https://schema.org","@type":"Joint","name":"膝关节"}',
            'Ligament' => '{"@context":"https://schema.org","@type":"Ligament","name":"前交叉韧带"}',
            'Muscle' => '{"@context":"https://schema.org","@type":"Muscle","name":"肱二头肌"}',
            'Nerve' => '{"@context":"https://schema.org","@type":"Nerve","name":"坐骨神经"}',
            'Vessel' => '{"@context":"https://schema.org","@type":"Vessel","name":"动脉"}',
            'Artery' => '{"@context":"https://schema.org","@type":"Artery","name":"主动脉"}',
            'LymphaticVessel' => '{"@context":"https://schema.org","@type":"LymphaticVessel","name":"淋巴管"}',
            'Vein' => '{"@context":"https://schema.org","@type":"Vein","name":"上腔静脉"}',
            'MedicalCode' => '{"@context":"https://schema.org","@type":"MedicalCode","name":"I10","codeValue":"I10","codingSystem":"ICD-10"}',
            'MedicalConditionStage' => '{"@context":"https://schema.org","@type":"MedicalConditionStage","name":"III期","stageAsNumber":3}',
            'DDxElement' => '{"@context":"https://schema.org","@type":"DDxElement","name":"鉴别诊断：偏头痛"}',
            'MedicalIntangible' => '{"@context":"https://schema.org","@type":"MedicalIntangible","name":"医学无形实体"}',
            'DrugPregnancyCategory' => '{"@context":"https://schema.org","@type":"DrugPregnancyCategory","name":"https://schema.org/FDAcategoryC"}',
            'DrugPrescriptionStatus' => '{"@context":"https://schema.org","@type":"DrugPrescriptionStatus","name":"https://schema.org/PrescriptionOnly"}',
            'DrugCostCategory' => '{"@context":"https://schema.org","@type":"DrugCostCategory","name":"https://schema.org/ReimbursementCap"}',
            'InfectiousAgentClass' => '{"@context":"https://schema.org","@type":"InfectiousAgentClass","name":"病毒"}',
            'MedicalDevicePurpose' => '{"@context":"https://schema.org","@type":"MedicalDevicePurpose","name":"治疗目的"}',
            'MedicalEvidenceLevel' => '{"@context":"https://schema.org","@type":"MedicalEvidenceLevel","name":"https://schema.org/EvidenceLevelA"}',
            'MedicalImagingTechnique' => '{"@context":"https://schema.org","@type":"MedicalImagingTechnique","name":"https://schema.org/CT"}',
            'MedicalObservationalStudyDesign' => '{"@context":"https://schema.org","@type":"MedicalObservationalStudyDesign","name":"https://schema.org/CohortStudy"}',
            'MedicalProcedureType' => '{"@context":"https://schema.org","@type":"MedicalProcedureType","name":"https://schema.org/SurgicalProcedure"}',
            'MedicalStudyStatus' => '{"@context":"https://schema.org","@type":"MedicalStudyStatus","name":"https://schema.org/Completed"}',
            'MedicalTrialDesign' => '{"@context":"https://schema.org","@type":"MedicalTrialDesign","name":"https://schema.org/DoubleBlindedTrial"}',
            'MedicineSystem' => '{"@context":"https://schema.org","@type":"MedicineSystem","name":"https://schema.org/WesternConventional"}',
            'PhysicalActivityCategory' => '{"@context":"https://schema.org","@type":"PhysicalActivityCategory","name":"https://schema.org/AerobicActivity"}',
            'PhysicalExam' => '{"@context":"https://schema.org","@type":"PhysicalExam","name":"https://schema.org/EyeExam"}',
            'MedicalSpecialty' => '{"@context":"https://schema.org","@type":"MedicalSpecialty","name":"https://schema.org/Cardiovascular"}',
            'Specialty' => '{"@context":"https://schema.org","@type":"Specialty","name":"医学专业"}',
            // ---- 网页元素 ----
            'AboutPage' => '{"@context":"https://schema.org","@type":"AboutPage","inLanguage":"zh-CN","name":"关于我们"}',
            'CheckoutPage' => '{"@context":"https://schema.org","@type":"CheckoutPage","name":"结算页"}',
            'CollectionPage' => '{"@context":"https://schema.org","@type":"CollectionPage","name":"作品合集"}',
            'ImageGallery' => '{"@context":"https://schema.org","@type":"ImageGallery","name":"图片画廊"}',
            'VideoGallery' => '{"@context":"https://schema.org","@type":"VideoGallery","name":"视频中心"}',
            'ContactPage' => '{"@context":"https://schema.org","@type":"ContactPage","name":"联系我们"}',
            'ItemPage' => '{"@context":"https://schema.org","@type":"ItemPage","name":"商品详情页"}',
            'MedicalWebPage' => '{"@context":"https://schema.org","@type":"MedicalWebPage","name":"健康百科页"}',
            'ProfilePage' => '{"@context":"https://schema.org","@type":"ProfilePage","name":"个人主页"}',
            'SearchResultsPage' => '{"@context":"https://schema.org","@type":"SearchResultsPage","name":"搜索结果页"}',
            'WebPageElement' => '{"@context":"https://schema.org","@type":"WebPageElement","name":"页面元素"}',
            'SiteNavigationElement' => '{"@context":"https://schema.org","@type":"SiteNavigationElement","name":"主导航"}',
            'Table' => '{"@context":"https://schema.org","@type":"Table","name":"数据表格"}',
            'WPAdBlock' => '{"@context":"https://schema.org","@type":"WPAdBlock","name":"广告区块"}',
            'WPFooter' => '{"@context":"https://schema.org","@type":"WPFooter","name":"页脚"}',
            'WPHeader' => '{"@context":"https://schema.org","@type":"WPHeader","name":"页眉"}',
            'WPSideBar' => '{"@context":"https://schema.org","@type":"WPSideBar","name":"侧边栏"}',
            // ---- 事件 ----
            'BusinessEvent' => '{"@context":"https://schema.org","@type":"BusinessEvent","name":"行业交流会","startDate":"2024-06-10T09:00:00+08:00"}',
            'ChildrensEvent' => '{"@context":"https://schema.org","@type":"ChildrensEvent","name":"亲子嘉年华","startDate":"2024-06-01T10:00:00+08:00"}',
            'ComedyEvent' => '{"@context":"https://schema.org","@type":"ComedyEvent","name":"脱口秀之夜","startDate":"2024-07-03T20:00:00+08:00"}',
            'DanceEvent' => '{"@context":"https://schema.org","@type":"DanceEvent","name":"现代舞展演","startDate":"2024-06-20T19:30:00+08:00"}',
            'EducationEvent' => '{"@context":"https://schema.org","@type":"EducationEvent","name":"公益科普讲座","startDate":"2024-06-15T14:00:00+08:00"}',
            'Festival' => '{"@context":"https://schema.org","@type":"Festival","name":"音乐节","startDate":"2024-08-01T00:00:00+08:00"}',
            'FoodEvent' => '{"@context":"https://schema.org","@type":"FoodEvent","name":"美食节","startDate":"2024-06-05T11:00:00+08:00"}',
            'LiteraryEvent' => '{"@context":"https://schema.org","@type":"LiteraryEvent","name":"新书签售会","startDate":"2024-06-18T15:00:00+08:00"}',
            'SaleEvent' => '{"@context":"https://schema.org","@type":"SaleEvent","name":"年中大促","startDate":"2024-06-18T00:00:00+08:00"}',
            'SocialEvent' => '{"@context":"https://schema.org","@type":"SocialEvent","name":"会员答谢晚宴","startDate":"2024-06-30T18:00:00+08:00"}',
            'SportsEvent' => '{"@context":"https://schema.org","@type":"SportsEvent","name":"城市马拉松","startDate":"2024-05-01T07:30:00+08:00"}',
            'TheaterEvent' => '{"@context":"https://schema.org","@type":"TheaterEvent","name":"话剧演出","startDate":"2024-07-01T19:00:00+08:00"}',
            'VisualArtsEvent' => '{"@context":"https://schema.org","@type":"VisualArtsEvent","name":"当代艺术展开展","startDate":"2024-06-12T10:00:00+08:00"}',
            'UserInteraction' => '{"@context":"https://schema.org","@type":"UserInteraction","about":"用户与某页面产生的交互事件"}',
            'UserBlocks' => '{"@context":"https://schema.org","@type":"UserBlocks","name":"用户屏蔽了该内容"}',
            'UserCheckins' => '{"@context":"https://schema.org","@type":"UserCheckins","name":"用户在此地点登记"}',
            'UserComments' => '{"@context":"https://schema.org","@type":"UserComments","commentText":"用户发表了评论"}',
            'UserDownloads' => '{"@context":"https://schema.org","@type":"UserDownloads","name":"用户下载了文件"}',
            'UserLikes' => '{"@context":"https://schema.org","@type":"UserLikes","name":"用户点赞了内容"}',
            'UserPageVisits' => '{"@context":"https://schema.org","@type":"UserPageVisits","name":"用户访问了页面"}',
            'UserPlays' => '{"@context":"https://schema.org","@type":"UserPlays","name":"用户播放了视频"}',
            'UserPlusOnes' => '{"@context":"https://schema.org","@type":"UserPlusOnes","name":"用户点击了+1"}',
            'UserTweets' => '{"@context":"https://schema.org","@type":"UserTweets","name":"用户发布了推文"}',
            // ---- 其它 ----
            'ScholarlyArticle' => '{"@context":"https://schema.org","@type":"ScholarlyArticle","headline":"人工智能在医疗中的应用","author":{"@type":"Person","name":"王教授"}}',
            'MedicalScholarlyArticle' => '{"@context":"https://schema.org","@type":"MedicalScholarlyArticle","headline":"慢性病管理的循证研究","author":{"@type":"Person","name":"李医生"}}',
            'Diet' => '{"@context":"https://schema.org","@type":"Diet","name":"地中海饮食","description":"以达到心血管健康为目标"}',
            'ExercisePlan' => '{"@context":"https://schema.org","@type":"ExercisePlan","name":"四周增肌计划","intensity":"中等强度"}',
            'MusicVideoObject' => '{"@context":"https://schema.org","@type":"MusicVideoObject","name":"官方MV视频","contentUrl":"https://example.com/mv.mp4"}',
            'MusicPlaylist' => '{"@context":"https://schema.org","@type":"MusicPlaylist","name":"晨间醒神歌单","numTracks":20}',
            'MusicRecording' => '{"@context":"https://schema.org","@type":"MusicRecording","name":"《夜空中最亮的星》","byArtist":{"@type":"MusicGroup","name":"示例乐队"}}',
            'DataType' => '{"@context":"https://schema.org","@type":"DataType","description":"基本数据类型，如整型、字符串等"}',
            'Audience' => '{"@context":"https://schema.org","@type":"Audience","name":"18-35岁年轻白领","audienceType":"成年消费者"}',
            'MedicalAudience' => '{"@context":"https://schema.org","@type":"MedicalAudience","name":"糖尿病患者群体","audienceType":"患者"}',
            'Quantity' => '{"@context":"https://schema.org","@type":"Quantity","description":"定量值，如距离、时间、质量等"}',
            'Distance' => '{"@context":"https://schema.org","@type":"Distance","name":"城市间距离","quantitativeValue":"120 千米"}',
            'Duration' => '{"@context":"https://schema.org","@type":"Duration","description":"持续时间，遵循 ISO 8601，如 PT1H"}',
            'Energy' => '{"@context":"https://schema.org","@type":"Energy","name":"能量","quantitativeValue":"500 千焦"}',
            'Mass' => '{"@context":"https://schema.org","@type":"Mass","name":"质量","quantitativeValue":"3 千克"}',
            'StructuredValue' => '{"@context":"https://schema.org","@type":"StructuredValue","name":"结构化值","description":"结构上有特定限制的值，如地址"}',
            'GeoShape' => '{"@context":"https://schema.org","@type":"GeoShape","name":"服务范围","polygon":"39.3,-84.2 39.3,-84.1 39.2,-84.1"}',
            'MedicalEnumeration' => '{"@context":"https://schema.org","@type":"MedicalEnumeration","description":"有关健康和医学实践的枚举"}',
            // ---- 教育 ----
            'School' => '{"@context":"https://schema.org","@type":"School","name":"市第一中学"}',
            'CollegeOrUniversity' => '{"@context":"https://schema.org","@type":"CollegeOrUniversity","name":"示例大学","url":"https://www.univ.example.com/"}',
            'ElementarySchool' => '{"@context":"https://schema.org","@type":"ElementarySchool","name":"实验小学"}',
            'HighSchool' => '{"@context":"https://schema.org","@type":"HighSchool","name":"实验高中"}',
            'MiddleSchool' => '{"@context":"https://schema.org","@type":"MiddleSchool","name":"第二初级中学"}',
            'Preschool' => '{"@context":"https://schema.org","@type":"Preschool","name":"阳光幼儿园"}',
            // ---- 商业分类父类 ----
            'FoodEstablishment' => '{"@context":"https://schema.org","@type":"FoodEstablishment","name":"示例餐厅","servesCuisine":"粤菜"}',
            'EntertainmentBusiness' => '{"@context":"https://schema.org","@type":"EntertainmentBusiness","name":"示例娱乐场所"}',
            'FinancialService' => '{"@context":"https://schema.org","@type":"FinancialService","name":"示例金融服务中心"}',
            'HealthAndBeautyBusiness' => '{"@context":"https://schema.org","@type":"HealthAndBeautyBusiness","name":"示例美业机构"}',
            'HomeAndConstructionBusiness' => '{"@context":"https://schema.org","@type":"HomeAndConstructionBusiness","name":"示例家装服务"}',
            'DryCleaningOrLaundry' => '{"@context":"https://schema.org","@type":"DryCleaningOrLaundry","name":"洁净干洗店"}',
            'AdultEntertainment' => '{"@context":"https://schema.org","@type":"AdultEntertainment","name":"示例娱乐场所"}',
            'BedAndBreakfast' => '{"@context":"https://schema.org","@type":"BedAndBreakfast","name":"暖心民宿","priceRange":"400-700"}',
            'Hostel' => '{"@context":"https://schema.org","@type":"Hostel","name":"青年旅舍","priceRange":"$"}',
            'Motel' => '{"@context":"https://schema.org","@type":"Motel","name":"公路汽车旅馆","priceRange":"$$"}',
            'DiagnosticLab' => '{"@context":"https://schema.org","@type":"DiagnosticLab","name":"中心检验所"}',
            'MedicalClinic' => '{"@context":"https://schema.org","@type":"MedicalClinic","name":"社区医疗站"}',
            'Optician' => '{"@context":"https://schema.org","@type":"Optician","name":"明视眼镜"}',
            'VeterinaryCare' => '{"@context":"https://schema.org","@type":"VeterinaryCare","name":"爱宠动物医院"}',
            'ProfessionalService' => '{"@context":"https://schema.org","@type":"ProfessionalService","name":"专业咨询公司"}',
            'Attorney' => '{"@context":"https://schema.org","@type":"Attorney","name":"正法律师事务所"}',
            'Notary' => '{"@context":"https://schema.org","@type":"Notary","name":"市公证处"}',
            'RadioStation' => '{"@context":"https://schema.org","@type":"RadioStation","name":"城市之声广播电台"}',
            'TelevisionStation' => '{"@context":"https://schema.org","@type":"TelevisionStation","name":"省电视台"}',
            'RealEstateAgent' => '{"@context":"https://schema.org","@type":"RealEstateAgent","name":"安居房产中介"}',
            // ---- 场所 ----
            'CivicStructure' => '{"@context":"https://schema.org","@type":"CivicStructure","name":"示例公共设施"}',
            'AdministrativeArea' => '{"@context":"https://schema.org","@type":"AdministrativeArea","name":"华东地区"}',
            'Aquarium' => '{"@context":"https://schema.org","@type":"Aquarium","name":"海洋世界水族馆"}',
            'BusStation' => '{"@context":"https://schema.org","@type":"BusStation","name":"客运总站"}',
            'BusStop' => '{"@context":"https://schema.org","@type":"BusStop","name":"人民广场站"}',
            'Campground' => '{"@context":"https://schema.org","@type":"Campground","name":"绿野露营地"}',
            'Cemetery' => '{"@context":"https://schema.org","@type":"Cemetery","name":"西山公墓"}',
            'Crematorium' => '{"@context":"https://schema.org","@type":"Crematorium","name":"市殡仪馆火化部"}',
            'EventVenue' => '{"@context":"https://schema.org","@type":"EventVenue","name":"会展中心"}',
            'GovernmentBuilding' => '{"@context":"https://schema.org","@type":"GovernmentBuilding","name":"市民中心大楼"}',
            'CityHall' => '{"@context":"https://schema.org","@type":"CityHall","name":"市政厅"}',
            'Courthouse' => '{"@context":"https://schema.org","@type":"Courthouse","name":"区人民法院"}',
            'DefenceEstablishment' => '{"@context":"https://schema.org","@type":"DefenceEstablishment","name":"国防机构驻地"}',
            'Embassy' => '{"@context":"https://schema.org","@type":"Embassy","name":"驻华大使馆"}',
            'LegislativeBuilding' => '{"@context":"https://schema.org","@type":"LegislativeBuilding","name":"市人大办公楼"}',
            'MusicVenue' => '{"@context":"https://schema.org","@type":"MusicVenue","name":"音乐厅"}',
            'ParkingFacility' => '{"@context":"https://schema.org","@type":"ParkingFacility","name":"中心停车场"}',
            'PerformingArtsTheater' => '{"@context":"https://schema.org","@type":"PerformingArtsTheater","name":"大剧院"}',
            'PlaceOfWorship' => '{"@context":"https://schema.org","@type":"PlaceOfWorship","name":"礼拜场所"}',
            'BuddhistTemple' => '{"@context":"https://schema.org","@type":"BuddhistTemple","name":"灵隐寺"}',
            'CatholicChurch' => '{"@context":"https://schema.org","@type":"CatholicChurch","name":"天主教教堂"}',
            'Church' => '{"@context":"https://schema.org","@type":"Church","name":"社区教堂"}',
            'HinduTemple' => '{"@context":"https://schema.org","@type":"HinduTemple","name":"印度教神庙"}',
            'Mosque' => '{"@context":"https://schema.org","@type":"Mosque","name":"清真寺"}',
            'Synagogue' => '{"@context":"https://schema.org","@type":"Synagogue","name":"会堂"}',
            'Playground' => '{"@context":"https://schema.org","@type":"Playground","name":"社区儿童乐园"}',
            'RVPark' => '{"@context":"https://schema.org","@type":"RVPark","name":"房车营地"}',
            'SubwayStation' => '{"@context":"https://schema.org","@type":"SubwayStation","name":"地铁一号线总站"}',
            'TaxiStand' => '{"@context":"https://schema.org","@type":"TaxiStand","name":"火车站出租车候客点"}',
            'TrainStation' => '{"@context":"https://schema.org","@type":"TrainStation","name":"高铁站"}',
            'Landform' => '{"@context":"https://schema.org","@type":"Landform","name":"示例地貌"}',
            'BodyOfWater' => '{"@context":"https://schema.org","@type":"BodyOfWater","name":"示例水体"}',
            'Canal' => '{"@context":"https://schema.org","@type":"Canal","name":"巴拿马运河"}',
            'Pond' => '{"@context":"https://schema.org","@type":"Pond","name":"村前池塘"}',
            'Reservoir' => '{"@context":"https://schema.org","@type":"Reservoir","name":"水库"}',
            'Waterfall' => '{"@context":"https://schema.org","@type":"Waterfall","name":"尼亚加拉瀑布"}',
            'LandmarksOrHistoricalBuildings' => '{"@context":"https://schema.org","@type":"LandmarksOrHistoricalBuildings","name":"圆明园遗址"}',
            'Residence' => '{"@context":"https://schema.org","@type":"Residence","name":"示例居所"}',
            'ApartmentComplex' => '{"@context":"https://schema.org","@type":"ApartmentComplex","name":"阳光花园小区"}',
            'GatedResidenceCommunity' => '{"@context":"https://schema.org","@type":"GatedResidenceCommunity","name":"别墅封闭社区"}',
            'SingleFamilyResidence' => '{"@context":"https://schema.org","@type":"SingleFamilyResidence","name":"独栋别墅"}',
        ];
    }

    public function handle(): int
    {
        $data = $this->examples();
        $updated = 0;
        $missing = 0;

        foreach ($data as $name => $example) {
            $type = SchemaType::where('name', $name)->where('status', 1)->first();
            if (!$type) {
                $missing++;
                $this->warn("未找到类型: {$name}");
                continue;
            }
            if ($this->option('dry')) {
                $this->info("DRY: {$name}");
                continue;
            }
            $type->update(['example' => $example]);
            $updated++;
        }

        $this->info("处理完成：写入 {$updated} 个，未找到 {$missing} 个。");

        return self::SUCCESS;
    }
}