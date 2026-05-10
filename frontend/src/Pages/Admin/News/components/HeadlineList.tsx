import type Article from "../Article.tsx";

interface Headline {
  title: string;
  source: string;
  sourceColor: string;
  article?: Article;
}

interface HeadlineListProps {
  headlines: Headline[];
  onSelect?: (article: Article) => void;
}

const HeadlineList = ({ headlines, onSelect }: HeadlineListProps) => {
  return (
    <div className="bg-card rounded-lg p-4">
      {headlines.map((h, i) => (
        <button
          key={i}
          className="w-full text-left py-3 border-b border-border last:border-0 flex items-start gap-3 hover:bg-secondary/50 -mx-1 px-1 rounded transition-colors"
          onClick={() => h.article && onSelect?.(h.article)}
        >
          <span
            className="w-6 h-6 rounded-full flex-shrink-0 flex items-center justify-center text-[10px] font-bold text-card mt-0.5"
            style={{ backgroundColor: h.sourceColor }}
          >
            {h.source.charAt(0)}
          </span>
          <span className="text-[15px] font-semibold leading-snug text-card-foreground">{h.title}</span>
        </button>
      ))}
    </div>
  );
};

export default HeadlineList;
