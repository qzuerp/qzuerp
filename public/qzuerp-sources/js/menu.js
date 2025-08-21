getInputs
  $(document).ready(function() {
    // Find the 'active' li element
    var activeLi = $('.treeview-menu li.active');

    // Check if an 'active' li element was found
    if (activeLi.length > 0) {
      // Traverse up the DOM and add 'active' class to parent li tags
      activeLi.parents('li.treeview').addClass('active');
    }
  });

  const menuFilterInput = document.getElementById("menuFilter");
  const menuItems = document.querySelectorAll(".sidebar-menu a");

  menuFilterInput.addEventListener("input", function () {
        const filterText = menuFilterInput.value.toLowerCase();

        menuItems.forEach(function (menuItem) {
            const menuItemText = menuItem.innerText.toLowerCase();
            const menuItemParent = menuItem.parentElement;

            if (menuItemText.includes(filterText) || menuItemParent.innerText.toLowerCase().includes(filterText)) {
                menuItem.style.display = "block";
                showParentMenu(menuItemParent);
            } else {
                menuItem.style.display = "none";
            }
        });
  });

  function showParentMenu(element) {
    if (element && element.classList.contains("treeview")) {
            const parentMenu = element.parentElement;
            parentMenu.style.display = "block";
            showParentMenu(parentMenu);
    }
  }
