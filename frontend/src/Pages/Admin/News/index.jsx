import React from "react";
import { createRoot } from "react-dom/client";
import App from './App.jsx';
import './css/global.css';
import './css/theme.css';
import './css/editor.css';
import './css/sections.css';
import './css/cards-boxes.css';

const root = createRoot(document.getElementById("root"));
root.render(
    <React.StrictMode>
        <App />
    </React.StrictMode>
);