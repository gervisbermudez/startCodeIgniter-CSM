class UploaderImages {

    constructor({data}){
        this.data = data;
      }

    static get toolbox() {
        return {
            title: 'Upload Imagen',
            icon: '<svg width="17" height="15" viewBox="0 0 336 276" xmlns="http://www.w3.org/2000/svg"><path d="M291 150V79c0-19-15-34-34-34H79c-19 0-34 15-34 34v42l67-44 81 72 56-29 42 30zm0 52l-43-30-56 30-81-67-66 39v23c0 19 15 34 34 34h178c17 0 31-13 34-29zM79 0h178c44 0 79 35 79 79v118c0 44-35 79-79 79H79c-44 0-79-35-79-79V79C0 35 35 0 79 0z"/></svg>'
        };
    }

    render() {
        let targetID = Date.now();

        let container = document.createElement('div');
        container.classList.add("img-upload-container");

        let imgContainer = document.createElement("div");
        imgContainer.classList.add("img-container");


        const img = document.createElement('img');
        this.data && this.data.url ? img.setAttribute("src", this.data.url) : '';
        this.data && this.data.url ? imgContainer.classList.add("added-image") : '';

        let changeIcon = document.createElement("i");
        changeIcon.innerText = "perm_media";
        changeIcon.classList.add("material-icons");
        changeIcon.classList.add("change-icon");
        changeIcon.setAttribute("title", "Cambiar Imagen");
        changeIcon.addEventListener("click", (event) => {
            PageNewForm.modalCallbackMode = "insertImage";
            PageNewForm.modalCallbackTargetID = targetID;
            container.setAttribute("id", targetID);
            imgContainer.classList.add("added-image");
            var elems = document.getElementById('fileUploader');
            var instance = M.Modal.init(elems, {});
            instance.open();
        });

        let button = document.createElement("button");
        button.innerHTML = "Open Modal";
        button.classList.add("btn")
        button.addEventListener("click", (event) => {
            PageNewForm.modalCallbackMode = "insertImage";

            PageNewForm.modalCallbackTargetID = targetID;
            container.setAttribute("id", targetID);
            imgContainer.classList.add("added-image");

            var elems = document.getElementById('fileUploader');
            var instance = M.Modal.init(elems, {});
            instance.open();
        });

        imgContainer.appendChild(img);
        imgContainer.appendChild(changeIcon);
        //imgContainer.appendChild(closeIcon);
        container.appendChild(imgContainer);
        container.appendChild(button);

        return container;
    }

    save(blockContent) {
        console.log({blockContent});
        let img = blockContent.querySelector('img');
        console.log({img});
        let url = img.getAttribute("src");
        return {
            url: url
        }
    }
}