import{C,D as E,q as V,j as P,e as R,o as G,a as n,b as q,t as J,d as D,x as W}from"./app-Ulf1_P01.js";import"./buttons.print-NLOxmzBh.js";/* empty css                                                */import"./jspdf.plugin.autotable-CqtI2_So.js";import{u as v,w as X}from"./xlsx-DjuO7_Ju.js";import{_ as K}from"./RHLayout-BFqH4W3s.js";const Q={class:"mt-0 bg-white p-4 shadow rounded-lg h-5/6"},Y={class:"overflow-x-auto"},Z={class:"text-sm leading-normal border-b border-gray-300"},tt={class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300",colspan:"2"},nt={__name:"FormarUnidades",props:{message:{String,default:""},color:{String,default:""},ruta:{type:Object},unidad:{type:Object},unidadesConOperador:{type:Object},operador:{type:Object},directivo:{type:Object},rolServicio:{type:Object},castigo:{type:Object},entrada:{type:Object},corte:{type:Object},ultimaCorrida:{type:Object},tipoUltimaCorrida:{type:Object},usuario:{type:Object}},setup(S){C.use(E),C.use(E);const d=S,b=new Date().toLocaleDateString(),T=I(new Date().getDay());function I(i){return["Domingo","Lunes","Martes","Miércoles","Jueves","Viernes","Sábado"][i]}V(()=>d.entrada.filter(i=>new Date(i.fechaRegistro).toLocaleDateString()===b));const F=()=>{W(()=>{const o=$("#formacionTablaId").DataTable().rows({search:"applied",page:"current"}).data();if(!o.length){console.warn("No hay datos para exportar.");return}const p=new Date().toISOString().split("T")[0],c=Object.fromEntries(d.ruta.map(a=>[a.idRuta,a.nombreRuta])),r=Object.fromEntries(d.rolServicio.map(a=>[a.idUnidad,a.trabajaDomingo])),t=Object.fromEntries(d.unidad.map(a=>[a.idUnidad,a.numeroUnidad])),e=Object.fromEntries(d.directivo.map(a=>[a.idDirectivo,a.nombre_completo])),l=Object.fromEntries(d.entrada.map(a=>[a.idUnidad,{horaEntrada:a.horaEntrada,tipoEntrada:a.tipoEntrada,extremo:a.extremo,created_at:a.created_at}]));Object.fromEntries(d.corte.map(a=>[a.idUnidad,[]]));const g=Object.fromEntries(d.ultimaCorrida.map(a=>[a.idUnidad,{tipoCorrida:a.idTipoUltimaCorrida,horaInicioUC:a.horaInicioUC,horaFinUC:a.horaFinUC,created_at:a.created_at}]));Object.fromEntries(d.castigo.map(a=>[a.idUnidad,[]]));const f=Object.fromEntries(d.operador.map(a=>[a.idOperador,a.nombre_completo])),O=Object.fromEntries(d.tipoUltimaCorrida.map(a=>[a.idTipoUltimaCorrida,a.tipoUltimaCorrida])),m=a=>{if(!a)return"";const[s,y]=a.split(":");return`${s}:${y}`},k=o.toArray().reduce((a,s)=>{var _,H;const y=l[s.idUnidad]||{},B=d.corte.filter(u=>u.idUnidad===s.idUnidad&&u.created_at.split("T")[0]===p),x=g[s.idUnidad]||{},j=d.castigo.filter(u=>u.idUnidad===s.idUnidad&&u.created_at.split("T")[0]===p);r[s.idUnidad];const U=((_=y.created_at)==null?void 0:_.split("T")[0])===p,h=((H=x.created_at)==null?void 0:H.split("T")[0])===p;return j.length>0,a[s.idUnidad]||(a[s.idUnidad]={ID:s.idUnidad,Ruta:c[s.idRuta]||"N/A","Trab. Domingo":r[s.idUnidad]||"Sin Asignar",Unidad:t[s.idUnidad]||"","Socio/Prestador":e[s.idDirectivo]||"","Hr. Entrada":U&&m(y.horaEntrada)||"","Tipo Entrada":U&&y.tipoEntrada||"",Extremo:U&&y.extremo||"","Hr. Corte":"",Causa:"","Hr. Regreso":"","Tipo De Corrida":h&&O[x.tipoCorrida]||"","Hr. Inicio UC":h&&m(x.horaInicioUC)||"","Hr. Regreso UC":h&&m(x.horaFinUC)||"","Hr. Inicio":"","Hr. Finaliza":"",Motivo:"","Otras Observaciones":"",Operador:f[s.idOperador]||"Sin Asignar"}),B.forEach(u=>{a[s.idUnidad]["Hr. Corte"]+=(a[s.idUnidad]["Hr. Corte"]?", ":"")+m(u.horaCorte),a[s.idUnidad].Causa+=(a[s.idUnidad].Causa?", ":"")+u.causa,a[s.idUnidad]["Hr. Regreso"]+=(a[s.idUnidad]["Hr. Regreso"]?", ":"")+m(u.horaRegreso)}),j.forEach(u=>{a[s.idUnidad]["Hr. Inicio"]+=(a[s.idUnidad]["Hr. Inicio"]?", ":"")+m(u.horaInicio),a[s.idUnidad]["Hr. Finaliza"]+=(a[s.idUnidad]["Hr. Finaliza"]?", ":"")+m(u.horaFin),a[s.idUnidad].Motivo+=(a[s.idUnidad].Motivo?", ":"")+u.castigo,a[s.idUnidad]["Otras Observaciones"]+=(a[s.idUnidad]["Otras Observaciones"]?", ":"")+u.observaciones}),a},{}),A=Object.values(k),N=v.json_to_sheet(A),w=v.book_new();v.book_append_sheet(w,N,"Formación De Unidades");const z=new Date().toISOString().split("T")[0];X(w,`Formación_De_Unidades_${z}.xlsx`)})},M=[{extend:"copyHtml5",text:'<i class="fa-solid fa-copy"></i> Copiar',className:"bg-cyan-500 hover:bg-cyan-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",exportOptions:{columns:[0,2]},button:!0},{title:"Formación De Unidades",text:'<i class="fa-solid fa-file-excel"></i> Excel',className:"bg-green-600 hover:bg-green-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",action:()=>F()},{title:"Formación De Unidades",extend:"print",text:'<i class="fa-solid fa-print"></i> Imprimir',className:"bg-blue-500 hover:bg-blue-600 text-white py-1/2 px-3 rounded mb-2 jump-icon",exportOptions:{columns:[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18]}}],L=[{data:null,render:function(i,o,p,c){return c.row+1}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.ruta.find(e=>e.idRuta===r.idRuta);return t?t.nombreRuta:""}else return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.rolServicio.filter(e=>e.idUnidad===r.idUnidad);return t.length>0?(t.sort((e,l)=>new Date(l.created_at)-new Date(e.created_at)),t[0].trabajaDomingo):'<span style="color: red;">Sin asignar</span>'}else return"Unidad no encontrada"}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);return r?r.numeroUnidad:""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.directivo.find(e=>e.idDirectivo===r.idDirectivo);return t?t.nombre_completo:""}else return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.entrada.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map(l=>l.horaEntrada.substring(0,5)).join(", ")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.entrada.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map(l=>l.tipoEntrada).join(", ")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.entrada.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map(l=>l.extremo).join(", ")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.corte.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map((l,g)=>`
            <div style="${t.length>1&&g!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">
              <div>${l.horaCorte.substring(0,5)}</div>
            </div>
          `).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.corte.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map((l,g)=>`
            <div style="${t.length>1&&g!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">
              <div>${l.causa}</div>
            </div>
          `).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.corte.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0)return t.map((l,g)=>{const f=l.horaRegreso?l.horaRegreso.substring(0,5):"";return`
            <div style="${t.length>1&&g!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">
              <div>${f}</div>
            </div>
          `}).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.ultimaCorrida.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0){const e=d.tipoUltimaCorrida.find(l=>l.idTipoUltimaCorrida===t[0].idTipoUltimaCorrida);if(e)return e.tipoUltimaCorrida}}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.ultimaCorrida.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0){const e=t[0].horaInicioUC;return e?e.substring(0,5):""}}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.ultimaCorrida.filter(e=>e.idUnidad===r.idUnidad&&new Date(e.created_at).toLocaleDateString()===b);if(t.length>0){const e=t[0].horaFinUC;if(e)return e.substring(0,5)}}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.castigo.filter(t=>t.idUnidad===i&&new Date(t.created_at).toLocaleDateString()===b);if(r.length>0){const t=r.map(e=>({horaInicio:e.horaInicio?e.horaInicio.substring(0,5):""}));return t.map((e,l)=>`
          <div style="${t.length>1&&l!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">
            <div>${e.horaInicio}</div>
          </div>
        `).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.castigo.filter(t=>t.idUnidad===i&&new Date(t.created_at).toLocaleDateString()===b);if(r.length>0){const t=r.map(e=>({horaFin:e.horaFin?e.horaFin.substring(0,5):""}));return t.map((e,l)=>`
          <div style="${t.length>1&&l!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">
            <div>${e.horaFin}</div>
          </div>
        `).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.castigo.filter(t=>t.idUnidad===i&&new Date(t.created_at).toLocaleDateString()===b);if(r.length>0){const t=r.map(e=>e.castigo||"");return t.map((e,l)=>`<div style="${t.length>1&&l!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">${e}</div>`).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.castigo.filter(t=>t.idUnidad===i&&new Date(t.created_at).toLocaleDateString()===b);if(r.length>0){const t=r.map(e=>e.observaciones||"");return t.map((e,l)=>`<div style="${t.length>1&&l!==t.length-1?"border-bottom: 1px solid #b2b2b2;":""}">${e}</div>`).join("")}return""}},{data:"idUnidad",render:function(i,o,p,c){const r=d.unidad.find(t=>t.idUnidad===i);if(r){const t=d.operador.find(e=>e.idOperador===r.idOperador);return t?t.nombre_completo:'<span style="color: red;">Sin asignar</span>'}else return""}}];return(i,o)=>(G(),P(K,{title:"Formar Unidades",usuario:d.usuario},{default:R(()=>[n("div",Q,[o[9]||(o[9]=n("h2",{class:"font-bold text-center text-xl pt-0"}," Formar Unidades",-1)),o[10]||(o[10]=n("div",{class:"bg-gradient-to-r from-cyan-300 to-cyan-500 h-px mb-1.5"},null,-1)),n("div",Y,[q(D(C),{class:"w-full table-auto text-sm display nowrap stripe compact cell-border order-column",id:"formacionTablaId",name:"formacionTablaId",columns:L,data:S.unidad,options:{responsive:!1,autoWidth:!1,dom:"Bftrip",language:{search:"Buscar",zeroRecords:"No hay registros para mostrar",info:"Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",infoEmpty:"Mostrando registros del 0 al 0 de un total de 0 registros",infoFiltered:"(filtrado de un total de _MAX_ registros)"},buttons:[M],paging:!1,lengthMenu:[]}},{default:R(()=>[n("thead",null,[n("tr",Z,[o[0]||(o[0]=n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"},null,-1)),n("th",tt,"FECHA: "+J(D(T)+", "+D(b)),1),o[1]||(o[1]=n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"},null,-1)),o[2]||(o[2]=n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"},null,-1)),o[3]||(o[3]=n("th",{class:"py-2 px-4 bg-green-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300 text-left",colspan:"3"},"ENTRADA",-1)),o[4]||(o[4]=n("th",{class:"py-2 px-4 bg-red-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300",colspan:"3"},"CORTE",-1)),o[5]||(o[5]=n("th",{class:"py-2 px-4 bg-blue-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300",colspan:"3"},"ÚLTIMAS CORRIDAS",-1)),o[6]||(o[6]=n("th",{class:"py-2 px-4 bg-yellow-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300",colspan:"4"},"CASTIGO",-1)),o[7]||(o[7]=n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"},null,-1))]),o[8]||(o[8]=n("tr",{class:"text-sm leading-normal border-b border-gray-300"},[n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," ID "),n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Ruta "),n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Trab. DOMINGO "),n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Unidad "),n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Socio / Prestador "),n("th",{class:"py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. entrada "),n("th",{class:"py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Tipo entrada "),n("th",{class:"py-2 px-4 bg-green-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Extremo "),n("th",{class:"py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. Corte "),n("th",{class:"py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Causa "),n("th",{class:"py-2 px-4 bg-red-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. regreso "),n("th",{class:"py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Tipo de Corrida "),n("th",{class:"py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. inicio "),n("th",{class:"py-2 px-4 bg-blue-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. regreso "),n("th",{class:"py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. inicio "),n("th",{class:"py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Hr. finaliza "),n("th",{class:"py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Motivo "),n("th",{class:"py-2 px-4 bg-yellow-100 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Otras observaciones "),n("th",{class:"py-2 px-4 bg-sky-200 font-bold uppercase text-sm text-grey-600 border-r border-grey-300"}," Operador ")],-1))])]),_:1},8,["data","options"])])])]),_:1},8,["usuario"]))}};export{nt as default};