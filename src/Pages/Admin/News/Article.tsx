interface Article {
    id: number;
    title: string;
    source: string;
    image_url?: string;
    content: string;
    published_at: string;
    author_name: string;
    //slug: string;
    category: string;
    url: string;
};

export default Article;