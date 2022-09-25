
const addFormToCollection = (e) => {
    const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);

    const item = document.createElement('li');

    item.innerHTML += collectionHolder
    .dataset
    .prototype
    .replace(
    /__name__/g,
    collectionHolder.dataset.index
    );

    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;

    item.classList.add('list-unstyled', 'justify-content-between');

    addVideoFormDeleteLink(item);
};

const addVideoFormDeleteLink = (item) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.className = "btn btn-info"
    removeFormButton.innerText = 'Supprimer cette vidéo';

    item.append(removeFormButton);

    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}

document
.querySelectorAll('.add_item_link')
.forEach(btn => {
    btn.addEventListener("click", addFormToCollection)
});

document
.querySelectorAll('ul.tags li')
.forEach((video) => {
    addVideoFormDeleteLink(video)
})

