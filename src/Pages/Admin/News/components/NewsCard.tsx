import { Heart, Share2, MoreVertical } from "lucide-react";
import { useState } from "react";
import Article from "../Article.tsx";

interface NewsCardProps {
  article: Article;
  onClick?: (article: Article) => void;
}

const NewsCard = ({ article, onClick }: NewsCardProps) => {
  const [liked, setLiked] = useState(false);
  const [sourceColor] = useState("#ffffff");

  return (
    <article
      className="bg-card rounded-lg overflow-hidden animate-fade-in cursor-pointer"
      onClick={() => onClick?.(article)}
    >
      {article.image_url && (
        <div className="w-full aspect-video bg-muted overflow-hidden">
          <img src={article.image_url} alt={article.title} className="w-full h-full object-cover" loading="lazy" />
        </div>
      )}
      <div className="p-4">
        <h3 className="text-base font-bold leading-snug text-card-foreground line-clamp-3 mb-2">{article.title}</h3>
        <div className="flex items-center justify-between">
          <div className="flex items-center gap-2">
            <span
              className="w-5 h-5 rounded-full flex items-center justify-center text-[9px] font-bold text-card"
              style={{ backgroundColor: sourceColor }}
            >
              {article.source.charAt(0)}
            </span>
            <span className="text-xs text-muted-foreground">{article.source}</span>
            <span className="text-xs text-muted-foreground">· {article.published_at}</span>
          </div>
          <div className="flex items-center gap-1">
            <button onClick={(e) => { e.stopPropagation(); setLiked(!liked); }} className="p-1.5 rounded-full hover:bg-secondary transition-colors">
              <Heart className={`w-4 h-4 ${liked ? "fill-red-500 text-red-500" : "text-muted-foreground"}`} />
            </button>
            <button onClick={(e) => e.stopPropagation()} className="p-1.5 rounded-full hover:bg-secondary transition-colors">
              <Share2 className="w-4 h-4 text-muted-foreground" />
            </button>
            <button onClick={(e) => e.stopPropagation()} className="p-1.5 rounded-full hover:bg-secondary transition-colors">
              <MoreVertical className="w-4 h-4 text-muted-foreground" />
            </button>
          </div>
        </div>
      </div>
    </article>
  );
};

export default NewsCard;
