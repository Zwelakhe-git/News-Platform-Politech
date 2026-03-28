// posting app
function showAlert(message, type, container) {
    // Remove existing alerts
    const existingAlerts = container.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type === 'success' ? 'ok' : 'err'}`;
    alertDiv.innerHTML = `
        <i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i>
        <span>${message}</span>
    `;
    
    container.appendChild(alertDiv);
    
    setTimeout(() => {
        if (alertDiv.parentNode) {
            alertDiv.remove();
        }
    }, 5000);
}
async function refreshData(name){
    try{
        let target = postableItems.find(item => item.name === name);
        if(target && target.contentUrl){
            let response = await fetch(target.contentUrl);
            let data = await response.json();
            target.data = data;
        }
    } catch(error){
        console.log(error.message);
    }
}
async function publishContent(name,dataSet){
    
    try{
        if(dataSet.position !== "mainpage"){
            dataSet.position = "yes";
        } else {
            // alert that item is already published
            showAlert(`${name} ${dataSet.id} already published`, "success", document.body);
            return;
        }
        let formData = new FormData();
        for(const [k, v] of Object.entries(dataSet)){
            formData.append(k,v);
        }
        let res = await fetch(`/admin/index.php?action=${name}&method=edit&id=${dataSet.id}`, {
            method: "POST",
            body: formData
        });
        
        console.log(res);
        if(res.redirected){
            let sp = (new URL(res.url)).searchParams;
            if(sp.get("success")){
                console.log("update successful");
                showAlert("success", "success", document.body);
                refreshData();
                // successful update
            } else {
                showAlert("fail", "error", document.body);
                // show failure alert
            }
        }
    } catch(error){
        console.log(error);
        console.log(dataSet);
    }
}

async function posting(){
var poster = document.querySelector(".poster");
var posterRect = poster.getClientRects()[0];
const numberRegex = /[0-9]+[.]?[0-9]+/;
const defaultPageContent = document.querySelector(".container.mt-4");
const externalModifiers = [
    {
        url: "/admin/views/layout/css/css-6966390.css",
        id: "css-6966390"
    }
]

let session = sessionStorage.getItem("user");
if(session === '[object Object]'){
    // error during setItem. parsed as object instead of string
}
session = JSON.parse(session);


if(!session || session.name === "guest"){
    return;
}

var musicData = []
var eventsData = []

const displaySettings = [
    "small",
    "big"
]
var currentDisplay = 0;


function fetchData(url, key = null, dest = null){
    var result;
    try{
        fetch(url)
        .then(res => res.json())
        .then(data => {
            if(dest?.[Symbol.iterator]){
                if(key){
                    result = dest.concat(data[key]);
                    return;
                }
                result = dest.concat(data);
            } else {
                console.log("failed to expand");
            }

        })
        .catch(error => {
            console.log(error);
        })
    } catch(error){
        console.log(error.message)
    }
    return result;
}
async function fetchData_(url, key = null, dest = null){
    try{
        let response = await fetch(url);
        let data = await response.json();
        if(dest?.[Symbol.iterator]){
            return key ? dest.concat(data[key]) : dest.concat(data);
        } else {
            return data;
        }
    } catch(error){
        console.log(error.message);
    }
    return null;
}


function optionsPanel(){
    const container = document.createElement("div");
    var containerModifier = {
        display: "flex",
        flexDirection: "column",
        justifyContent: "space-evenly",
        gap: "10px",
        flexWrap: "wrap",
        padding: "10px",
        borderRadius: "10px",
        background: "white",
        //boxShadow: "0px 0px 5px grey",
        overflow: "auto",
        transition: "all 0.3s ease"
    }
    container.id = "options-container";
    for(const [k,v] of Object.entries(containerModifier)){
        container.style[`${k}`] = v;
    }
    let html = "";
    postableItems.forEach((item, i) => {
        html += `
            <div class="bg-white full-w column flex pad-10 rounded-circle-shape shadow">
                <div class="section-info">
                    <h5>${item.name}</h5>
                </div>
                <div class="flex row wrap full-w" style="margin: auto">
        `;
        if(item.data.length == 0){
            html += `
            <div class="full-w flex column center">
                <h2 style="margin: 10px 0px">No ${item.name} added</h2>
                <a href="${item.url}" class="no-text-deco pad-10 rounded-circle-shape"
                style="background: #0f51d6; color: #ffffff">add ${item.name}</a>
            </div>`
        } else {
            item.data.forEach((d, j) => {
                html += `<a class="option-item" style="
                    padding: 5px;
                    border-radius: 10px;
                    text-align: center;
                    transition: all 0.3s linear;
                    "
                    data-itemid="${item.name}-${j}"
                >
                    <div class="img-container full-wh"
                    style="
                    ${(d.image_location || d.urlToImage) ? `
                        background: url(${d.image_location ?? d.urlToImage});
                        background-repeat: no-repeat;
                        background-size: cover;` : ""}
                    ">
                    </div>
                    <!--<h5 style="margin: 5px 0px 0px;" class="item-title">${d.title ?? ""}</h5>-->
                </a>`;
            });
        }
        
        html += "</div></div>";
    });
    container.innerHTML += html;
    return container;
}

function initEventListeners(){
    let optionItems = document.querySelectorAll(".option-item");

    optionItems.forEach(item => {
        item.addEventListener("click", ()=>{
            item.classList.toggle("selected");
        });
    })
}
function handleDisplayChange(newValue){
    let container = document.querySelector("#options-container");
    if(!container) return;
    if(currentDisplay == newValue) return

    let options = container.querySelectorAll(".option-item");
    if(options.length == 0) return;

    currentDisplay = newValue;
    options.forEach(opt => {
        opt.style.height = currentDisplay == 0 ? "fit-content" : "150px";
    });
    
}

function render(){
    /**
     * display the items to be posted by the user.
     * hide the current page content then render the selection modal
     */
    let options = document.querySelector("#options-container");
    const container = document.createElement("div");
    container.style.top = defaultPageContent.style.top;
    container.style.width = "80%";
    container.style.margin = "auto";
    if(!options){
        options = optionsPanel();
        document.body.appendChild(options);
    };

    let buttons = document.createElement("div");
    buttons.className = "flex row";
    buttons.style.gap = "10px";
    buttons.style.marginTop = "20px";
    
    buttons.innerHTML +=`
    <button type="button" class="pad-10 no-border rounded-circle-shape" id="display-btn"
     style="background: #d10a07;
         display: none;
         color: white;
         width: fit-content !important">change display</button>
     
     <button type="button" class="pad-10 no-border rounded-circle-shape" id="close-btn"
     style="background: #d10a07;
         color: white;
         width: fit-content !important">close</button>

     <button type="button" class="pad-10 no-border rounded-circle-shape" id="save-btn"
     style="background: #13df10;
         color: white;
         width: fit-content !important">save</button>
    `;
    
    container.appendChild(options);
    container.appendChild(buttons);
    
    document.body.appendChild(container);

    setTimeout(()=>{
        document.querySelector("#display-btn").addEventListener('click', ()=>{
            handleDisplayChange((currentDisplay + 1) % displaySettings.length);
        });
        document.querySelector("#close-btn").addEventListener('click', ()=>{
            options.parentElement.remove();
            //buttons.remove();
            externalModifiers.forEach(modifier=>{
                if(document.querySelector(`link[href='${modifier.url}']`)){
                    let el = document.querySelector(`link[href='${modifier.url}']`);
                    el.parentElement.removeChild(el);
                }
            })
            //document.querySelector("#poster")
            poster.style.display = "block";
            defaultPageContent.style.display = "block";
        });
        document.querySelector("#save-btn").addEventListener('click', async ()=>{
            try{
                const selectedItems = document.querySelectorAll(".option-item.selected");
                if(Array.from(selectedItems).length === 0) return;
                
                // fetch user info to know if they subscribed
                let response = await fetch(`/php/dbReader.php?q=getUserProfile&username=${session.name}`);
                let userInfo = await response.json();
                //console.log(userInfo);
                if(userInfo.response != "success"){
                    // redirect
                    console.log("failed to get user info: ", userInfo.response);
                } else if(!(userInfo.premiumnSubscription && userInfo.premiumnSubscription === "active")){
                    // redirect to payment if not subscribed
                    window.location.href = "/premium/premium.html";
                    return;
                } else {
                    // put selected items to main page if subscription is active
                    Array.from(selectedItems).forEach(el => {
                        let [name, i] = el.dataset.itemid.split("-");
                        let source = postableItems.find(item => item.name == name);
                        let target = source.data[Number(i)];
                        //console.log(name, i, source,target);
                        console.log("updating");
                        publishContent(name, target);
                    });
                }
                
            } catch(error){
                console.log(error);
            }
        });
    }, 200);
}

[musicData, eventsData] = await Promise.all([
    fetchData_(`/php/dbReader.php?r=musicContent&owner=${session.name}`, null, musicData),
    fetchData_(`/php/dbReader.php?r=events&owner=${session.name}`, null, eventsData)
]);

var postableItems = [
    {
        name: "music",
        url: "?action=music&method=create",
        contetUrl: `/php/dbReader.php?r=musicContent&owner=${session.name}`,
        data: musicData
    },
    {
        name: "events",
        url: "?action=events&method=create",
        contetUrl: `/php/dbReader.php?r=events&owner=${session.name}`,
        data: eventsData
    }
]
//console.log(musicData.length, eventsData.length, session, session.name);
poster.addEventListener('click', (e)=>{
    
    externalModifiers.forEach(modifier => {
        if(document.querySelector(`link[href='${modifier.url}']`)){
            let el = document.querySelector(`link[href='${modifier.url}']`);
            el.parentElement.removeChild(el);
        }
        let tag = document.createElement("link");
        tag.rel = "stylesheet";
        tag.id = modifier.id;
        tag.href = modifier.url;
        document.head.appendChild(tag);
    });
    render();
    defaultPageContent.style.display = "none";
    poster.style.display = "none";
    setTimeout(()=>{
        initEventListeners();
    }, 200);
});

};