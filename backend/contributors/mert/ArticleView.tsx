import { ArrowLeft, Heart, Share2, ExternalLink } from "lucide-react";
import { useState } from "react";
import type Article from "../Article.tsx";

interface ArticleViewProps {
  article: Article;
  onBack: () => void;
}

const ArticleView = ({ article, onBack }: ArticleViewProps) => {
  const [liked, setLiked] = useState(false);

  const formattedContent = article.content
    ? article.content.replace(/\\r\\n|\\n/g, "\n").split("\n").filter(Boolean)
    : [];

  return (
    <div className="min-h-screen bg-background animate-fade-in">
      {/* Top bar */}
      <div className="sticky top-0 z-50 flex items-center justify-between px-4 h-14 bg-background/90 backdrop-blur border-b border-border">
        <button
          onClick={onBack}
          className="flex items-center gap-2 text-sm font-medium text-muted-foreground hover:text-foreground transition-colors"
        >
          <ArrowLeft className="w-5 h-5" />
          Назад
        </button>
        <div className="flex items-center gap-1">
          <button
            onClick={() => setLiked(!liked)}
            className="p-2 rounded-full hover:bg-secondary transition-colors"
          >
            <Heart className={`w-5 h-5 ${liked ? "fill-red-500 text-red-500" : "text-muted-foreground"}`} />
          </button>
          <button className="p-2 rounded-full hover:bg-secondary transition-colors">
            <Share2 className="w-5 h-5 text-muted-foreground" />
          </button>
        </div>
      </div>

      <article className="max-w-lg mx-auto pb-24">
        {/* Cover image */}
        {article.image_url && (
          <div className="w-full aspect-video bg-muted overflow-hidden">
            <img
              src={article.image_url}
              alt={article.title}
              className="w-full h-full object-cover"
            />
          </div>
        )}

        <div className="px-4 pt-5">
          {/* Category badge */}
          {article.category && (
            <span className="inline-block text-xs font-bold uppercase tracking-wider text-primary bg-primary/10 px-2.5 py-1 rounded-full mb-3">
              {article.category}
            </span>
          )}

          {/* Title */}
          <h1 className="text-xl font-extrabold leading-snug text-foreground mb-4">
            {article.title}
          </h1>

          {/* Meta */}
          <div className="flex items-center gap-2 mb-5 pb-5 border-b border-border">
            <span className="w-7 h-7 rounded-full bg-primary flex items-center justify-center text-[10px] font-bold text-primary-foreground flex-shrink-0">
              {article.source?.charAt(0) ?? "?"}
            </span>
            <div className="flex flex-col">
              <span className="text-sm font-semibold text-foreground">{article.source}</span>
              {article.author_name && (
                <span className="text-xs text-muted-foreground">{article.author_name}</span>
              )}
            </div>
            <span className="text-xs text-muted-foreground ml-auto">{article.published_at}</span>
          </div>

          {/* Content */}
          {formattedContent.length > 0 ? (
            <div className="space-y-4">
              {formattedContent.map((para, i) => (
                <p key={i} className="text-[15px] leading-relaxed text-foreground/90">
                  {para}
                </p>
              ))}
            </div>
          ) : (
            <p className="text-sm text-muted-foreground italic">Текст статьи недоступен.</p>
          )}

          {/* Source link */}
          {article.url && (
            <a
              href={article.url}
              target="_blank"
              rel="noopener noreferrer"
              className="mt-8 flex items-center gap-2 text-sm font-medium text-primary hover:underline"
            >
              <ExternalLink className="w-4 h-4" />
              Читать оригинал на сайте источника
            </a>
          )}
        </div>
      </article>
    </div>
  );
};

export default ArticleView;
