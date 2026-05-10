import { useEffect, useState } from "react";
import VkurseHeader from "@/components/VkurseHeader";
import VkurseTabs from "@/components/VkurseTabs";
import VkurseBottomNav from "@/components/VkurseBottomNav";
import CurrencyBar from "@/components/CurrencyBar";
import HeadlineList from "@/components/HeadlineList";
import NewsCard from "@/components/NewsCard";
import PublisherCard from "@/components/PublisherCard";
import ProductCard from "@/components/ProductCard";
import FreshNewsList from "@/components/FreshNewsList";
import ArticleView from "@/components/ArticleView";
import productJacket from "@/assets/product-jacket.jpg";
import newsCables from "@/assets/news-cables.jpg";
import newsChurch from "@/assets/news-church.jpg";
import type Article from "../Article.tsx";

const API_BASE = "http://100.96.104.125/vkurse/api/v1/news";
const API_KEY  = "vkurse_69cf890f379993_54239691";

async function fetchNews(category: string, count: number): Promise<Article[]> {
  const res = await fetch(`${API_BASE}/${category}/${API_KEY}/${count}`);
  if (!res.ok) return [];
  const json = await res.json();
  // API response: { success, data: { articles: [...] } }
  if (json?.data?.articles) return json.data.articles as Article[];
  // fallback: direct array
  if (Array.isArray(json)) return json as Article[];
  return [];
}

const SOURCE_COLORS: Record<string, string> = {
  CNN: "#cc0000", BBC: "#bb1919", ТАСС: "#1a56db",
  РИА: "#2563eb", Lenta: "#7c3aed",
};
function sourceColor(src: string) {
  return SOURCE_COLORS[src] ?? "#374151";
}

const MainFeed = ({ onArticleClick }: { onArticleClick: (a: Article) => void }) => {
  const [articles, setArticles] = useState<Article[]>([]);
  useEffect(() => {
    fetchNews("sports", 70).then(setArticles);
  }, []);

  const headlines = articles.slice(0, 4).map((a) => ({
    title: a.title,
    source: a.source,
    sourceColor: sourceColor(a.source),
    article: a,
  }));

  const freshItems = articles.slice(10, 14).map((a) => ({
    title: a.title,
    source: a.source,
    timeAgo: a.published_at,
    article: a,
  }));

  return (
    <div className="flex flex-col gap-2 px-2">
      <CurrencyBar />
      {headlines.length > 0 && <HeadlineList headlines={headlines} onSelect={onArticleClick} />}
      {articles.slice(4, 14).map((article) => (
        <NewsCard key={article.id} article={article} onClick={onArticleClick} />
      ))}
      <ProductCard
        title="Куртка утепленная Bulmer"
        description="Ваш личный ИИ-стилист и тренды весны 2026 уже..."
        imageUrl={productJacket}
        rating={5}
        reviewCount="1 356 467"
        source="Lamoda: мода, красота, дом"
        adLabel="Ad 16+"
      />
      <PublisherCard
        name="Российская газета"
        avatar="https://ui-avatars.com/api/?name=РГ&background=dc2626&color=fff&size=64"
        timeAgo="1 день"
        title="Розы и молитва. Как в Тбилиси простились с патриархом Илией II"
        preview="В Тбилиси простились с патриархом Илией II. Тбилиси не спало несколько суток. Во..."
        imageUrl={newsChurch}
        verified
      />
      {freshItems.length > 0 && <FreshNewsList items={freshItems} onSelect={onArticleClick} />}
      {articles.slice(14, 70).map((article) => (
        <NewsCard key={article.id} article={article} onClick={onArticleClick} />
      ))}
    </div>
  );
}

const SpbFeed = ({ onArticleClick }: { onArticleClick: (a: Article) => void }) => (
<div className="flex flex-col gap-2 px-2">
  <div className="bg-card rounded-lg p-4">
    <div className="flex items-center justify-between mb-3">
      <h2 className="text-xl font-bold text-card-foreground">Санкт-Петербург</h2>
      <button className="text-sm text-muted-foreground bg-secondary px-3 py-1 rounded-full">Ещё</button>
    </div>
  </div>
  <NewsCard
    article={{ id: 1, title: "В Петербурге оштрафовали провайдера за свободный доступ к YouTube", source: "ТАСС", published_at: "Час назад", image_url: newsCables, content: "", author_name: "", category: "Регион", url: "" }}
    onClick={onArticleClick}
  />
  <NewsCard
    article={{ id: 2, title: "Случай заражения оспой обезьян зафиксирован в Санкт-Петербурге", source: "Известия", published_at: "3 часа назад", content: "", author_name: "", category: "Здоровье", url: "adw" }}
    onClick={onArticleClick}
  />
</div>
);

const FreshFeed = ({ onArticleClick }: { onArticleClick: (a: Article) => void }) => {
  const [articles, setArticles] = useState<Article[]>([]);
  useEffect(() => {
    fetchNews("sports", 50).then(setArticles);
  }, []);
  const freshItems = articles.map((a) => ({
    title: a.title,
    source: a.source,
    timeAgo: a.published_at,
    article: a,
  }));
  return (
    <div className="flex flex-col gap-2 px-2">
      <FreshNewsList items={freshItems} onSelect={onArticleClick} />
    </div>
  );
}

const Index = () => {
  const [activeTab, setActiveTab] = useState("Главное");
  const [selectedArticle, setSelectedArticle] = useState<Article | null>(null);

  if (selectedArticle) {
    return (
      <div className="max-w-lg mx-auto">
        <ArticleView article={selectedArticle} onBack={() => setSelectedArticle(null)} />
      </div>
    );
  }

  return (
    <div className="min-h-screen bg-background max-w-lg mx-auto">
      <VkurseHeader />
      <VkurseTabs activeTab={activeTab} onTabChange={setActiveTab} />
      <div className="h-[52px]" />
      <main className="pb-20 pt-3">
        {activeTab === "Главное" && <MainFeed onArticleClick={setSelectedArticle} />}
        {activeTab === "Санкт-Петербург" && <SpbFeed onArticleClick={setSelectedArticle} />}
        {activeTab === "Свежее" && <FreshFeed onArticleClick={setSelectedArticle} />}
      </main>
      <VkurseBottomNav />
    </div>
  );
};

export default Index;
