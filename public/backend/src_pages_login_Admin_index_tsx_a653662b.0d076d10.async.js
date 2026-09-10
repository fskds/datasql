(globalThis.utooChunk_ant_design_pro||(globalThis.utooChunk_ant_design_pro=[])).push(["object"==typeof document?document.currentScript:void 0,42115,e=>{"use strict";var a=e.i(91398),i=e.i(35898),t=e.i(96643),n=e.i(91788),o=e.i(30943);async function r(e,a){return e={...e,grant_type:"password",client_id:"019fcf86-9a02-70c2-8664-b4cf7e2b53a6",client_secret:"WnFEITQAu3l82lvuo6sbpb1DcFfurK96aeIaLgWS",scope:""},(0,i.request)("/oauth/token",{method:"POST",headers:{"Content-Type":"application/json"},data:e,...a||{}})}var s=e.i(46525),l=e.i(27830);let p=l.keyframes`
  0%            { opacity: 0; z-index: 3; }
  16.67%        { opacity: 1; z-index: 3; }
  33.33%        { opacity: 1; z-index: 3; }
  33.34%        { opacity: 1; z-index: 2; }
  50%           { opacity: 1; z-index: 2; }
  50.01%        { opacity: 0; z-index: 1; }
  100%          { opacity: 0; z-index: 1; }
`,d=l.keyframes`
  0%            { opacity: 0; z-index: 1; }
  33.33%        { opacity: 0; z-index: 1; }
  33.34%        { opacity: 0; z-index: 3; }
  50%           { opacity: 1; z-index: 3; }
  66.67%        { opacity: 1; z-index: 3; }
  66.68%        { opacity: 1; z-index: 2; }
  83.33%        { opacity: 1; z-index: 2; }
  83.34%        { opacity: 0; z-index: 1; }
  100%          { opacity: 0; z-index: 1; }
`,g=l.keyframes`
  0%            { opacity: 1; z-index: 2; }
  16.67%        { opacity: 1; z-index: 2; }
  16.68%        { opacity: 0; z-index: 1; }
  33.33%        { opacity: 0; z-index: 1; }
  33.34%        { opacity: 0; z-index: 1; }
  66.67%        { opacity: 0; z-index: 1; }
  66.68%        { opacity: 0; z-index: 3; }
  83.33%        { opacity: 1; z-index: 3; }
  99.99%        { opacity: 1; z-index: 3; }
  100%          { opacity: 1; z-index: 2; }
`,c=(0,l.createStyles)(()=>({container:{position:"fixed",top:0,left:0,width:"100%",height:"100vh",overflow:"hidden",fontFamily:"'PT Sans', 'Microsoft YaHei', Helvetica, Arial, sans-serif",textAlign:"center",color:"#fff","& .bg-layer":{position:"absolute",top:0,left:0,width:"100%",height:"100%",backgroundSize:"cover",backgroundPosition:"center"},"& .bg-layer-1":{backgroundImage:"url(/images/login-bg/1.jpg)",animation:`${p} 18s linear infinite`},"& .bg-layer-2":{backgroundImage:"url(/images/login-bg/2.jpg)",animation:`${d} 18s linear infinite`},"& .bg-layer-3":{backgroundImage:"url(/images/login-bg/3.jpg)",animation:`${g} 18s linear infinite`},"*, *:after, *:before":{boxSizing:"border-box",margin:0,padding:0,outline:"none"},"& .page-container":{position:"relative",zIndex:10,margin:"140px auto 0 auto"},"& h1":{fontSize:"30px",fontWeight:700,textShadow:"0 1px 4px rgba(0, 0, 0, .2)",margin:0},"& form":{position:"relative",width:"305px",margin:"15px auto 0 auto",textAlign:"center"},"& input":{width:"300px",height:"42px",lineHeight:"42px",marginTop:"25px",padding:"0 15px",background:"rgba(45, 45, 45, .15)",borderRadius:"6px",border:"1px solid rgba(255, 255, 255, .15)",boxShadow:"0 2px 3px 0 rgba(0, 0, 0, .1) inset",fontFamily:"'PT Sans', 'Microsoft YaHei', Helvetica, Arial, sans-serif",fontSize:"14px",color:"#fff",textShadow:"0 1px 2px rgba(0, 0, 0, .1)",transition:"all .3s ease","&::placeholder":{color:"#fff",opacity:.35},"&:-ms-input-placeholder":{color:"#fff"},"&::-webkit-input-placeholder":{color:"#fff"},"&:focus":{outline:"none",border:"1px solid rgba(255, 255, 255, .25)",boxShadow:`
            0 2px 3px 0 rgba(0, 0, 0, .1) inset,
            0 0 20px 0 rgba(239, 67, 0, .3),
            0 2px 10px 0 rgba(0, 0, 0, .3)
          `}},"& button":{cursor:"pointer",width:"300px",height:"44px",marginTop:"25px",padding:0,background:"#ef4300",borderRadius:"6px",border:0,boxShadow:`
          0 15px 30px 0 rgba(255, 255, 255, .25) inset,
          0 2px 7px 0 rgba(0, 0, 0, .2)
        `,fontFamily:"'PT Sans', 'Microsoft YaHei', Helvetica, Arial, sans-serif",fontSize:"14px",fontWeight:700,color:"#fff",textShadow:"0 1px 2px rgba(0, 0, 0, .1)",transition:"all .2s","&:hover":{boxShadow:`
            0 15px 30px 0 rgba(255, 255, 255, .15) inset,
            0 2px 7px 0 rgba(0, 0, 0, .2)
          `},"&:active":{boxShadow:`
            0 5px 8px 0 rgba(0, 0, 0, .1) inset,
            0 1px 4px 0 rgba(0, 0, 0, .1)
          `},"&:disabled":{opacity:.6,cursor:"not-allowed"}},"& .lang-wrap":{position:"fixed",right:"16px",top:"16px",zIndex:10}}})),x=()=>(0,a.jsx)("div",{className:"lang-wrap",children:i.SelectLang&&(0,a.jsx)(i.SelectLang,{})});var u=()=>{let[e,l]=(0,n.useState)(""),[p,d]=(0,n.useState)(""),[g,u]=(0,n.useState)(!1),{initialState:f,setInitialState:b}=(0,i.useModel)("@@initialState"),{styles:h}=c(),{message:y}=t.App.useApp(),m=(0,i.useIntl)(),w=async()=>{let e=await f?.fetchUserInfo?.();e&&(0,o.flushSync)(()=>{b(a=>({...a,currentUser:e}))})},z=async a=>{if(a.preventDefault(),!e||!p)return void y.warning("请输入用户名和密码");u(!0);try{let a=await r({username:e,password:p,type:"account"});if("Bearer"===a.token_type){y.success(m.formatMessage({id:"pages.login.success",defaultMessage:"登录成功！"})),localStorage.setItem("token",a.access_token),await w();let e=new URL(window.location.href).searchParams,i=(e=>{if(!e?.startsWith("/")||e.startsWith("//"))return"/";try{let a=new URL(e,window.location.origin);if(a.origin!==window.location.origin)return"/";return`${a.pathname}${a.search}${a.hash}`}catch{return"/"}})(e.get("redirect"));window.location.href=i;return}y.error(a?.message||"登录失败")}catch(e){y.error(m.formatMessage({id:"pages.login.failure",defaultMessage:"登录失败，请重试！"}))}finally{u(!1)}};return(0,a.jsxs)("div",{className:h.container,children:[(0,a.jsx)(i.Helmet,{children:(0,a.jsxs)("title",{children:[m.formatMessage({id:"menu.login",defaultMessage:"登录页"}),s.default.title&&` - ${s.default.title}`]})}),(0,a.jsx)(x,{}),(0,a.jsx)("div",{className:"bg-layer bg-layer-1"}),(0,a.jsx)("div",{className:"bg-layer bg-layer-2"}),(0,a.jsx)("div",{className:"bg-layer bg-layer-3"}),(0,a.jsxs)("div",{className:"page-container",children:[(0,a.jsx)("h1",{children:m.formatMessage({id:"menu.login",defaultMessage:"Login"})}),(0,a.jsxs)("form",{onSubmit:z,children:[(0,a.jsx)("input",{type:"text",name:"username",value:e,onChange:e=>l(e.target.value),placeholder:m.formatMessage({id:"pages.login.username",defaultMessage:"Username"})}),(0,a.jsx)("input",{type:"password",name:"password",value:p,onChange:e=>d(e.target.value),placeholder:m.formatMessage({id:"pages.login.password",defaultMessage:"Password"})}),(0,a.jsx)("button",{type:"submit",disabled:g,children:g?m.formatMessage({id:"pages.login.loading",defaultMessage:"Signing in..."}):m.formatMessage({id:"pages.login.submit",defaultMessage:"Sign in"})})]})]})]})};e.s(["default",()=>u],42115)}]);