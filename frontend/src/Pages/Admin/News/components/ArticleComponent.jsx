import React from "react";

export default function ArticleComponent({article, setArticlesList, setEditMode, setCurrentArticleId}){
    const editArticle = ()=>{
        setCurrentArticleId(article.id);
        setEditMode(true);
    };
    const deleteArticle = async ()=>{
        if(!confirm("Are you sure you want to delete this article?")) return;

        setArticlesList(prev => prev.filter(other => other.id !== article.id));
        // cache data
    };
    return (<div className='article-card'>
        <div className="article-card-image">
                <img alt="article-image" src={article.cover_image_url}/>
            </div>
            <div className="article-card__body">
                <div className="article-info">
                    <div className="article-category">{article.category}</div>
                    <div className="article-date">
                        <i className="fa-regular fa-clock"></i>
                        <span>{article.published_at}</span>
                    </div>
                </div>
                <div className="article-card-descr">
                    <p>{article.title}</p>
                </div>
            </div>
            <div className="article-card__footer">
                <div className="article-card-date">{article.updated_at}</div>
                <div className="article-card-author">
                    <div className="article-card-author-avatar">
                        <img alt="author avatar" src={article.avatar_url}/>
                    </div>
                    <div className="article-card-author-name">{article.author_name}</div>
                </div>
                <div className="edit-actions">
                    <button type="button" className="delete-btn btn-danger">
                        
                    </button>
                    <button type="button" className="edit-btn btn-warn"></button>
                </div>
            </div>
    </div>);
}