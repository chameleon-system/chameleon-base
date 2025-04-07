/*
  these javascript functions are required by the CMS Module CMSModuleChooser
*/
function CreateModuleInstance(sModuleSpotName, moduleID, view) {
    var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
    formObj.moduleid.value = moduleID;
    formObj.view.value = view;
    formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'NewInstance';
    formObj.submit();
}

function ClearModuleInstance(sModuleSpotName) {
    var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
    formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'ClearInstance';
    formObj.submit();
}

function DeleteModuleInstance(sModuleSpotName) {
    if (confirm(CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.confirm_module_content_delete'))) {
        var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
        formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'DeleteInstance';
        formObj.submit();
    }
}

function CopyModuleInstance(sModuleSpotName) {
    var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
    formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'CopyInstance';
    formObj.submit();
}

function ChangeView(sModuleSpotName, view) {
    var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
    formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'ChangeView';
    formObj.view.value = view;
    formObj.submit();
}

function EditTable(tableID, moduleInstanceID, sessionid) {
    window.open("/cms?pagedef=tablemanager&sRestrictionField=cms_tpl_module_instance_id&sRestriction=" + moduleInstanceID + "&id=" + tableID, "_top", "");
}

function Rename(sModuleSpotName, instanceName) {
    var newName = '';
    var defaultValue = instanceName;
    // @todo load message from translation
    var message = CHAMELEON.CORE.i18n.Translate('chameleon_system_core.js.confirm_new_name')
    newName = window.prompt(message, defaultValue);
    if (newName != null && newName != defaultValue) {
        var formObj = document.getElementsByName('moduleblock' + sModuleSpotName)[0];
        formObj.instancename.value = newName;
        formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'RenameInstance';
        formObj.submit();
    }
}

function LoadModuleInstance(sModuleSpotName, pageid, portalId){
    document.location.href = '/cms?pagedef=templateengineplain&_mode=load_module&id=' + pageid + '&spotname=' + sModuleSpotName + '&portalId=' + portalId;
}

function LoadModuleInstanceCopy(sModuleSpotName, pageid, portalId){
    document.location.href = '/cms?pagedef=templateengineplain&_mode=load_module&id=' + pageid + '&spotname=' + sModuleSpotName + '&portalId=' + portalId + '&bLoadCopy=1';
}

function SwitchModuleInstances(sModuleSpotName, sTargetModuleSpotName) {
    var formObj = document.forms['moduleblock' + sModuleSpotName];
    formObj.elements['module_fnc[' + sModuleSpotName + ']'].value = 'SwitchInstances';
    var targetfield = document.createElement("input");
    targetfield.setAttribute("type", "hidden");
    targetfield.setAttribute("value", sTargetModuleSpotName);
    targetfield.setAttribute("name", "sTargetModuleSpotName");
    formObj.appendChild(targetfield);
    formObj.submit();
}

function EditCmsMasterSpot(controller, tableid, id) {
    top.window.location.href = controller + '?pagedef=tableeditor&tableid=' + tableid + '&id=' + id;
}

/* esono tree menu */
var treeMenuHeight = 260;
var treeMenuHeaderHeight = 20;
var lastTopMainMenu = 0;

function openMenuLevel(element) {
    const listItem = element.closest("li");
    const submenu = listItem.querySelector("ul");

    if (!submenu) return;

    if (listItem.classList.contains("active")) {
        listItem.classList.remove("active");
        element.classList.remove("expanded");
        submenu.style.display = "none";
    } else {
        // close all other menus
        document.querySelectorAll(".moduleChooserMenu ul").forEach(ul => {
            if (ul !== submenu) {
                ul.style.display = "none";
                ul.closest("li")?.classList.remove("active");
            }
        });

        // open the clicked menu
        listItem.classList.add("active");
        element.classList.add("expanded");
        submenu.style.display = "block";
    }
}

function initModuleChooser() {
    document.querySelectorAll(".moduleChooserMenu .CMSModuleChooserCrosshair").forEach(item => {
        item.setAttribute("draggable", "true");

        item.addEventListener("dragstart", event => {
            event.dataTransfer.setData("text/plain", event.target.dataset.spotname);
            item.classList.add("dragging");

            // empty image to prevent default drag image
            const img = new Image();
            img.src = "data:image/gif;base64,R0lGODlhAQABAAD/ACwAAAAAAQABAAACADs=";
            event.dataTransfer.setDragImage(img, 0, 0);
        });

        item.addEventListener("dragend", () => {
            document.querySelectorAll(".moduleChooserMenu .dragging").forEach(el => el.classList.remove("dragging"));
        });
    });

    document.querySelectorAll(".moduleChooserMenu .CMSModuleChooserTarget").forEach(target => {
        target.addEventListener("dragover", event => {
            event.preventDefault();
            target.classList.add("drop-target");
        });

        target.addEventListener("dragleave", event => {
            if (!event.relatedTarget || !target.contains(event.relatedTarget)) {
                target.classList.remove("drop-target");
            }
        });

        target.addEventListener("drop", event => {
            event.preventDefault();
            const sourceSpot = event.dataTransfer.getData("text/plain");
            const targetSpot = target.dataset.spotname;

            if (sourceSpot && targetSpot && sourceSpot !== targetSpot) {
                SwitchModuleInstances(sourceSpot, targetSpot);
            }

            target.classList.remove("drop-target");
        });
    });
}

function closeModuleMenu() {
    cmsModuleMenu.style.display = "none";
    document.querySelectorAll("#cmsModuleMenu ul").forEach(ul => {
        ul.style.display = "none";
    });
    document.querySelectorAll("#cmsModuleMenu li.active").forEach(activeItem => {
        activeItem.classList.remove("active");
    });

    const topLevelUl = document.querySelector("#cmsModuleMenu #menuWrapper > ul");
    if (topLevelUl) {
        topLevelUl.style.display = "block";
    }
}

function makeDraggable(element, handle) {
    let offsetX, offsetY, isDragging = false;

    handle.addEventListener("mousedown", (event) => {
        isDragging = true;
        offsetX = event.clientX - element.getBoundingClientRect().left;
        offsetY = event.clientY - element.getBoundingClientRect().top;
        event.preventDefault();
    });

    document.addEventListener("mousemove", (event) => {
        if (isDragging) {
            element.style.left = `${event.clientX - offsetX}px`;
            element.style.top = `${event.clientY - offsetY}px`;
        }
    });

    document.addEventListener("mouseup", () => {
        isDragging = false;
    });
}

document.addEventListener("DOMContentLoaded", function () {
    initModuleChooser();

    if (!document.getElementById("cmsModuleMenu")) {
        const cmsModuleMenu = document.createElement("div");
        cmsModuleMenu.id = "cmsModuleMenu";
        cmsModuleMenu.style.display = "none";
        document.body.appendChild(cmsModuleMenu);
    }

    document.querySelectorAll(".moduleChooserMenu .cmsModuleMenuLauncher").forEach(launcher => {
        launcher.addEventListener("click", event => {
            event.preventDefault();

            const x = event.pageX;
            const y = event.pageY;
            const container = launcher.closest(".moduleChooserMenu");

            cmsModuleMenu.style.top = `${y}px`;
            cmsModuleMenu.style.left = `${x}px`;

            cmsModuleMenu.innerHTML = `
                <div class="moduleMenuHeader">
                    <span id="closeModuleMenu">
                        <i class="fas fa-window-close"></i>
                    </span>
                </div>
                <div id="menuWrapper">${container.querySelector(".moduleChooserMenuTree").innerHTML}</div>
            `;

            const rootUl = cmsModuleMenu.querySelector("#menuWrapper > ul");
            if (rootUl) {
                rootUl.style.display = "block";
            }

            cmsModuleMenu.style.display = "block";

            document.getElementById("closeModuleMenu").addEventListener("click", closeModuleMenu);

            document.querySelectorAll("#cmsModuleMenu .moduleInstanceSwitcher").forEach(switcher => {
                switcher.addEventListener("click", () => {
                    SwitchModuleInstances(switcher.dataset.sourcespot, switcher.dataset.targetspot);
                });

                switcher.addEventListener("mouseover", () => {
                    const targetSpot = document.getElementById("CMSModuleChooserTarget" + switcher.dataset.targetspot);
                    if (targetSpot) {
                        targetSpot.style.border = "1px red solid";
                    }
                });

                switcher.addEventListener("mouseout", () => {
                    const targetSpot = document.getElementById("CMSModuleChooserTarget" + switcher.dataset.targetspot);
                    if (targetSpot) {
                        targetSpot.style.border = "1px green solid";
                    }
                });
            });

            makeDraggable(cmsModuleMenu, cmsModuleMenu.querySelector(".moduleMenuHeader"));
        });
    });
});
