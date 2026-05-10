import { ArrowRight } from "lucide-react";
import type Article from "../Article.tsx";

interface FreshNewsItem {
  title: string;
  source: string;
  timeAgo: string;
  article?: Article;
}

interface FreshNewsListProps {
  items: FreshNewsItem[];
  onSelect?: (article: Article) => void;
}

const FreshNewsList = ({ items, onSelect }: FreshNewsListProps) => {
  return (
    <div className="bg-card rounded-lg overflow-hidden animate-fade-in">
      <div className="flex items-center justify-between p-4 pb-0">
        <h2 className="text-xl font-bold text-card-foreground">Свежее</h2>
        <ArrowRight className="w-5 h-5 text-card-foreground" />
      </div>
      <div className="p-4 pt-2">
        {items.map((item, i) => (
          <button
            key={i}
            className="w-full text-left py-3 border-b border-border last:border-0 hover:bg-secondary/30 -mx-1 px-1 rounded transition-colors"
            onClick={() => item.article && onSelect?.(item.article)}
          >
            <h3 className="text-[15px] font-bold leading-snug text-card-foreground mb-1">{item.title}</h3>
            <p className="text-xs text-muted-foreground">{item.source} · {item.timeAgo}</p>
          </button>
        ))}
      </div>
    </div>
  );
};

export default FreshNewsList;
