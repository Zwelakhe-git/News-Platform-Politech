import React, { useState, useEffect, useRef } from "react";
import config from "../config.js";

const API_KEY = "vkurse_69cf890f379993_54239691";
function PreviewComponent({aticle, setPreview}){
    return (
    <div className="article-priview">
        <div className="section-nav right flex w-full">
            <button type="button" className="btn btn-primary" onClick={()=>{setPreview(false);}}>Continue editing</button>
        </div>
        <div className="preview-body"></div>
    </div>);
}
export default function EditorComponent({setAlertModal, setAlertMessage, currentArticle, setCurrentArticleId, setEditMode}){
    const [title, setTitle] = useState(currentArticle ? currentArticle.title : '');
    const [category, setCategory] = useState(currentArticle ? currentArticle.category : '');
    const [content, setContent] = useState(currentArticle ? currentArticle.content : '');
    const [excerpt, setExcerpt] = useState(currentArticle ? currentArticle.excerpt : '');
    const [coverImageUrl, setCoverImageUrl] = useState(currentArticle ? currentArticle.cover_image_url : null);
    const [status, setStatus] = useState(currentArticle ? currentArticle.status : 'draft');
    const [uploadedImagesList, setUploadedImagesList] = useState([]);
    const [preview, setPreview] = useState(false);
    const [coverImageFile, setCoverImageFile] = useState(null);

    const editorInitialized = useRef(false);

    const uploadCoverImage = function(e){
        if(e.target.files && e.target.files[0]){
            setCoverImageFile(e.target.files[0]);
            const reader = new FileReader();
            reader.onload = (e)=>{
                const blob = e.target.result;
                setCoverImageUrl(blob);
            }
            reader.readAsDataURL(e.target.files[0]);
        }
        
    }
    
    const promptExit = ()=>{
        let changed = title.length > 0 || category.length > 0 || content.length > 0 || coverImageUrl != null;
        if(changed && !confirm("Are you sure you want to discard all changes?")) return;
        removeRedundantUploads();
        closeEditor();
    };
    const formSubmitHandler = async (e)=>{
        e.preventDefault();
        let valid = true;

        if(content.length === 0){
            alert("Content should not be empty");
            return;
        }
        
        const formData = new FormData();
        formData.append("title", title);
        formData.append("category", category);
        formData.append("content", content);
        formData.append("status", status);
        formData.append("news_image", coverImageFile);
        formData.append("excerpt", excerpt);
        let res = await fetch(`${config.adminArticleApi}/${currentArticle ? 'edit' : 'create'}?key=${config.apiKey}`,{
            method: 'POST',
            body: formData
        });
        try{
            let data = await res.json();
            if(data.success){
                setAlertMessage({message: data.message, type: 'success'});
                setAlertModal(true);
                closeEditor();
            } else {
                setAlertMessage({message: data.message, type: 'fail'});
                setAlertModal(true);
                console.log(data.message);
            }
            sessionStorage.removeItem('uploadedImages');
        } catch(err){
            console.log(err);
        }
    };
    const closeEditor = async ()=>{
        setCurrentArticleId(-1);
        setEditMode(false);
    };

    const removeRedundantUploads = async () => {
        if(!sessionStorage.getItem('uploadedImages')) return;
        try{
            let imageData = JSON.parse(sessionStorage.getItem('uploadedImages'));
            let responses = await Promise.all(imageData.map(data => {
                const formData = new FormData();
                let url = `${config.tmpUploadApi}/image/delete?key=vkurse_69cf890f379993_54239691`;
                if(data.deleteUrl && data.deleteUrl.length > 0){
                    url += '&dest=imgbb';
                    const [_, imageId, imageHash] = (new URL(data.deleteUrl)).pathname.split('/');
                    
                    formData.append('deleteUrl', data.deleteUrl);
                    formData.append('imageId', imageId);
                    formData.append('imageHash', imageHash);

                    return fetch(url, {
                        method: 'POST',
                        data: formData
                    });
                }
                url += `&id=${data.id}`;
                
                return fetch(url, {
                    method: 'POST',
                    data: formData
                });
            }));
            let data = await Promise.all(responses.map(res => res.json()));
            let failed = data.filter( d => !d.success );
            
            if(failed.length > 0){
                console.log(`${failed.length} files failed to delete and are remaining in the server`);
                for(let i = 0; i < failed.length; ++i){
                    console.log(failed[i].message);
                }
            }
        } catch(err){
            console.log(err);
        } finally {
            sessionStorage.removeItem('uploadedImages');
        }
    }
    const filesDeleted = useRef(false);
    const onPageLoad = ()=>{
        let navigation = performance.getEntriesByType('navigation')[0];
        if(navigation?.type === 'reload'){
            removeRedundantUploads();
        }
    };

    const initEditor = () => {
        if(!editorInitialized.current){
            console.log('initializing tinymce');
            config.tinymceFullEditor();
        }
    };

    const destroyEditor = () => {
        if(tinymce.get('content')){
            tinymce.get('content').remove();
            editorInitialized.current = false;
        }
    };
    
    useEffect(()=>{
        if(!preview){
            destroyEditor();
            setTimeout(initEditor, 50);
        } else {
            destroyEditor();
        }
        return ()=>{
            destroyEditor();
        }
    }, [preview]);

    useEffect(()=>{
        if(filesDeleted.current) return;
        filesDeleted.current = true;
        onPageLoad();
    }, []);

    window.TmcContentHandler = ()=>{
        try{
            console.log('changing content in tinymce');
            setContent(tinymce.get('content').getContent());
        } catch(err){
            console.log(err);
        }
    };
    window.TmcUploadHandler = async function(blobInfo, progress){
        let formData = new FormData();
        formData.append('image', blobInfo.blob());
        
        
        try{
            // ``
            let response = await fetch(`${config.tmpUploadApi}/image/save?key=vkurse_69cf890f379993_54239691`, {
                method: 'POST',
                body: formData
            });
            let result = await response.json();
            if(result.success){
                const imageData = {
                    id: result.data.id,
                    url: result.data.url,
                    displayUrl: result.data.display_url || '',
                    deleteUrl: result.data.delete_url || ''
                }
                setUploadedImagesList(prev => {
                    let newValue = [...prev, imageData];
                    sessionStorage.setItem('uploadedImages', JSON.stringify(newValue));
                    return newValue;
                });
                
                console.log(imageData)
                return imageData.displayUrl || imageData.url;
            } else {
                console.log(result.message);
            }
            throw new Error("Failed to upload file to server");
        } catch(err){
            console.log('TmcUploadHandler Error: ', err);
            throw new Error(err.message);
        }
        
    }
    return (<>
    
        <div className="section">
            <div className="section-info">
                <h2 className="section-title fa-3x fw-bolder">{preview ? 'Preview': 
                    (currentArticle ? 'Edit Article' : 'Create Article')}
                </h2>
            </div>
            {preview ? 
            <PreviewComponent article={{title, content, category, excerpt, coverImageUrl, status}} setPreview={setPreview}/>:
            <div className="edit-area">
                <div className="form-box">
                    <form className="form" id="edit-form" encType="multipart/form-data" onSubmit={formSubmitHandler}>
                        <div className="row">
                            <div className="col-md-8">
                                <div className="field mb-3">
                                    <label htmlFor="title" className="form-label">Title<span className="required">*</span></label>
                                    <input type="text" name="title" className="form-control" value={title} id="title" onChange={(e)=>{setTitle(e.target.value)}} required/>
                                </div>
                                <div className="field mb-3">
                                    <label htmlFor="category" className="form-label">Category<span className="required">*</span></label>
                                    <input type="text" name="category" className="form-control" value={category} id="category" onChange={(e)=>{setCategory(e.target.value)}} required/>
                                </div>
                            </div>
                            <div className="col-md-4">
                                <div className="field">
                                    <label htmlFor="cover-image" className="form-label">Cover Image</label>
                                    <input type="file" name="cover_image_url" className="form-control" id="cover-image-url" onChange={uploadCoverImage} required/>
                                </div>
                                <div className="article-image preview mt-3 text-center">
                                    <div className="cover-image-box">
                                        {coverImageUrl ?
                                        (<img src={coverImageUrl} alt="image" className="img-thumbnail"/>):
                                        (<div id="noImgPreview" className="text-muted border rounded p-4">
                                            <i className="fas fa-image icon"></i><br/>
                                            <span>Article image</span>
                                        </div>)
                                        }
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div className="field mb-3">
                            <label htmlFor="content" className="form-label">Content<span className="required">*</span></label>
                            <textarea type="text" name="content" id="content" className="tinymce-editor form-control"></textarea>
                        </div>
                        <div className="field mb-3">
                            <label htmlFor="status" className="form-label">Status<span className="required">*</span></label>
                            <select name="status" id="status" className="form-control" onChange={(e)=>setStatus(e.target.value)}>
                                <option value="draft">Draft</option>
                                <option value="published" defaultChecked={true}>Publish</option>
                            </select>
                        </div>
                        <div className="terms mb-3">
                            <div className="terms-field">
                                <input type="checkbox" name="privacy-terms" id="privacy-terms" required/>
                                <label htmlFor="privacy-terms">I agree to full disclosure of public content and share personal Email</label>
                            </div>
                            <div className="terms-field">
                                <input type="checkbox" name="conditions-terms" id="conditions-terms" required/>
                                <label htmlFor="conditions-terms">I agree to all conditions</label>
                            </div>
                        </div>
                        <div className="form-btns mb-3 flex">
                            <button type="submit" className="ok-btn btn btn-primary">Save</button>
                            <button type="button" className="cancel-btn warn btn btn-secondary" onClick={promptExit}>Cancel</button>
                            <button type="button" className="cancel-btn warn btn btn-secondary" onClick={() => {setPreview(true);}}>Preview</button>
                        </div>
                    </form>
                </div>
            </div> }
        </div>
    </>);
}