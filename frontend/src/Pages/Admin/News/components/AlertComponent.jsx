import React, { useEffect, useState } from "react";

export default function AlertComponent({message}){
    const [open, setOpen] = useState(true);
    const [visible, setVisible] = useState(true);
    useEffect(()=>{
        if(!visible) return;
        setVisible(false);
        setTimeout(()=>{setOpen(false)}, 5000);
    }, [visible]);
    
    return (<div className="alert" style={{
        position: 'absolute',
        top: '10px',
        right: '10px',
        transition: 'transform 0.3s linear',
        display: open ? 'none' : 'block',
        transform: visible ? 'translateX(0%)' : 'translateX(100%)'
        }}>
        <div className="alert-message">{message.message}</div>
        <div className="close-icon-box">
            <i className="fa-circle fa-x"></i>
        </div>
    </div>);
}