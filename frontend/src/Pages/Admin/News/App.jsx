import React, {useState, useEffect, useRef} from 'react';
import EditorComponent from './components/EditorComponent.jsx';
import ArticleComponent from './components/ArticleComponent.jsx';
import AlertComponent from './components/AlertComponent.jsx';
import ArticleView from './components/ArticleView';


function AllArticles({props}){
    const openEditor = ()=>{
        props.setEditMode(true);
    };
    return (<>
    <div className='section'>
        <div className=' section-info'>
            <h1 className='section-title fa-3x fw-bolder'>My Articles</h1>
        </div>
        <div className='section-body'>
            {props.articlesList.length === 0 ?
            (<div className='container w-full'>
                <div className='flex col center'>
                    <h1 className='fa-3x fw-bolder'>You Do not have any articles</h1>
                    <button type="button" className='btn btn-primary' onClick={openEditor}>Add article</button>
                </div>
            </div>) : (
            <div className='container w-full'>
                <div className='quick-actions flex'>
                    <div className='quick-actions-list-box'>
                        <button type="button" className="btn btn-primary" onClick={openEditor}>Add article</button>
                    </div>
                </div>
                <div className='articles-grid'>
                    {props.articlesList.map(article => (<ArticleView key={article.id} article={article}
                                                    setArticlesList={props.setArticlesList}
                                                    setEditMode={props.setEditMode}
                                                    setCurrentArticleId={props.setCurrentArtileId} />))}
                </div>
            </div>)
            }
        </div>
    </div>
    </>)
}

function App(){
    const [alertModal, setAlertModal] = useState(false);
    const [currentArticleId, setCurrentArticleId] = useState(-1);
    const [editMode, setEditMode] = useState(false);
    const [articlesList, setArticlesList] = useState([]);
    const [currentTabIndx, setCurrentTabIndx] = useState(0);
    const [alertMessage, setAlertMessage] = useState({message: '', type: ''});
    const articlesLoaded = useRef(false);
    useEffect(()=>{
        const load = async ()=>{
            try {
                let aid = {};
                if(sessionStorage.getItem('user')){
                    aid = JSON.parse(sessionStorage.getItem('user')).id;
                }
                let response = await fetch(`/vkurse/api/v1/news/all/vkurse_69cf890f379993_54239691/all?aid=${aid}`);
                let result = await response.json();
                if(result.success){
                    setArticlesList(result.data.articles);
                }
            } catch(err){
                console.error(err);
            }
        };
        if(!editMode && !articlesLoaded.current){
            load();
            articlesLoaded.current = true;
        }
        
    }, [editMode]);
    const isAllArticlesTab = currentTabIndx === 0;
    const isViewMode = !editMode;
    return (<>
    {/* {alertModal && <AlertComponent message={alertMessage}/>} */}
    {isViewMode && isAllArticlesTab && (
        <AllArticles props={{articlesList, setArticlesList, setEditMode, setCurrentArticleId}}/>
    )}
    {editMode && <EditorComponent currentArticle={articlesList.find(article => article.id === currentArticleId)}
                                    setAlertModal={setAlertModal}
                                    setAlertMessage={setAlertMessage}
                                    setCurrentArticleId={setCurrentArticleId}
                                    setEditMode={setEditMode}
    />}
    </>);
}

export default App;

