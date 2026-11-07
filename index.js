document.addEventListener('DOMContentLoaded', function() {

    const hamburgerToggle = document.querySelector(".hamburger");
    const nav = document.querySelector(".nav");

    if (hamburgerToggle && nav) {
        hamburgerToggle.addEventListener("click", () => {
            nav.classList.toggle("show");
        });
    }

    const langMenuItems = document.querySelectorAll(".lang-menu li");
    const currentLangText = document.getElementById("current-lang-text");

    // Default language
    let currentLang = localStorage.getItem("lang") || "en";
    loadLanguage(currentLang);

    // Click handler for language switch
    langMenuItems.forEach((item) => {
        item.addEventListener("click", function () {
            const selectedLang = this.getAttribute("data-lang");
            currentLang = selectedLang;
            localStorage.setItem("lang", currentLang);
            loadLanguage(currentLang);
            currentLangText.textContent = currentLang.toUpperCase();
        });
    });

    // Load and apply language file
    function loadLanguage(lang) {
        fetch(`lang/${lang}.json`)
        .then((res) => res.json())
        .then((translations) => {
            document.querySelectorAll("[data-i18n]").forEach((el) => {
                const key = el.getAttribute("data-i18n");
                const translated = getNestedTranslation(translations, key);
                if (translated) {
                    el.innerHTML = translated;
                }
            });

            // Handle placeholder and title attributes specifically
            document.querySelectorAll("[data-i18n-placeholder]").forEach((el) => {
                const key = el.getAttribute("data-i18n-placeholder");
                const translated = getNestedTranslation(translations, key);
                if (translated) {
                    el.setAttribute("placeholder", translated);
                }
            });

            document.querySelectorAll("[data-i18n-title]").forEach((el) => {
                const key = el.getAttribute("data-i18n-title");
                const translated = getNestedTranslation(translations, key);
                if (translated) {
                    el.setAttribute("title", translated);
                }
            });

            // Update all dynamic content that needs translation
            updateDynamicContent(translations);
        })
        .catch((err) => console.error("Language load error:", err));
    }

    // Handle nested keys like "hero.title"
    function getNestedTranslation(obj, key) {
        return key.split(".").reduce((o, i) => (o ? o[i] : null), obj);
    }
    
    // This new function will be called every time the language changes.
    function updateDynamicContent(translations) {
        // ========== Story and Intern Video Popup ==========
        const stories = {
            1: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story1.text"),
            },
            2: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story2.text"),
            },
            3: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story3.text"),
            },
            4: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story4.text"),
            },
            5: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story5.text"),
            },
            6: {
                video: "videos/11.mp4",
                poster: "images/office.jpg",
                text: getNestedTranslation(translations, "stories.story6.text"),
            },
        };
        const storyCards = document.querySelectorAll(".story-card");
        const storyVideo = document.getElementById("storyVideo");
        const storyText = document.getElementById("storyText");
        const storyPopup = document.getElementById("storyPopup");
        const closeStory = document.getElementById("closeStory");
        const speedSelect = document.getElementById("speed");

        if (storyCards.length && storyPopup && storyVideo && storyText && closeStory) {
            // Remove previous event listeners to avoid duplicates
            storyCards.forEach(card => {
                card.replaceWith(card.cloneNode(true));
            });
            const newStoryCards = document.querySelectorAll(".story-card");
            newStoryCards.forEach((card) => {
                card.addEventListener("click", () => {
                    const id = card.dataset.story;
                    if (stories[id]) {
                        storyVideo.src = stories[id].video;
                        storyVideo.poster = stories[id].poster;
                        storyText.innerHTML = stories[id].text;
                        storyPopup.style.display = "flex";
                        storyVideo.play();
                    }
                });
            });

            closeStory.addEventListener("click", () => {
                storyPopup.style.display = "none";
                storyVideo.pause();
                storyVideo.currentTime = 0;
                storyVideo.src = "";
            });
            
            if (speedSelect) {
                speedSelect.addEventListener("change", (e) => {
                    storyVideo.playbackRate = parseFloat(e.target.value);
                });
            }
            storyPopup.addEventListener('click', (e) => {
                if (e.target === storyPopup) {
                    closeStory.click();
                }
            });
        }


        // ========== Event Descriptions ==========
        const items = document.querySelectorAll(".event-item");
        items.forEach((item) => {
            const key = item.getAttribute("data-i18n-description");
            const translatedDesc = getNestedTranslation(translations, key);
            if (translatedDesc) {
                item.setAttribute("data-description", translatedDesc);
            }
        });

        // Set the active language text in the dropdown
        currentLangText.textContent = currentLang.toUpperCase();
    }


    let lastScrollTop = 0;
    const header = document.querySelector('.header');
    const backToTopButton = document.getElementById('backToTop');
    const navLinks = document.querySelectorAll('.nav-link');

    // Header hide/show on scroll
    window.addEventListener('scroll', function() {
        let scrollTop = window.pageYOffset || document.documentElement.scrollTop;

        if (scrollTop > lastScrollTop && scrollTop > header.offsetHeight) {
            // Scrolling down and scrolled past header
            header.style.transform = 'translateY(-100%)';
        } else {
            // Scrolling up
            header.style.transform = 'translateY(0)';
        }
        lastScrollTop = scrollTop;

        // Back to top button visibility
        if (scrollTop > 200) { // Show button after scrolling down 200px
            backToTopButton.style.display = 'block';
        } else {
            backToTopButton.style.display = 'none';
        }
    });

    // Back to top button smoother scroll
    backToTopButton.addEventListener('click', function(e) {
      e.preventDefault();

      const scrollDuration = 800; // in milliseconds
      const start = window.scrollY;
      const startTime = performance.now();

      function scrollStep(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / scrollDuration, 1); // from 0 to 1
        const ease = 1 - Math.pow(1 - progress, 3); // ease-out cubic
        window.scrollTo(0, start * (1 - ease));

        if (progress < 1) {
          requestAnimationFrame(scrollStep);
        }
      }

      requestAnimationFrame(scrollStep);
    });


    // Set active navigation link based on scroll position (optional, but good for UX)
    function setActiveNavLink() {
        let currentSection = '';
        const sections = document.querySelectorAll('section[id], body');

        sections.forEach(section => {
            if (!section.id && section.tagName !== 'BODY') return;

            const sectionTop = section.offsetTop - header.offsetHeight; // Adjust for fixed header
            const sectionHeight = section.clientHeight;
            if (pageYOffset >= sectionTop && pageYOffset < sectionTop + sectionHeight) {
                currentSection = section.getAttribute('id') || 'top'; // Use 'top' for body
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            const linkHref = link.getAttribute('href');

            if (window.pageYOffset === 0 && linkHref === '#top') {
                link.classList.add('active');
            } else if (linkHref.includes(currentSection) && currentSection !== 'top') {
                link.classList.add('active');
            }
        });
    }

    // Call setActiveNavLink on scroll and page load
    window.addEventListener('scroll', setActiveNavLink);
    setActiveNavLink(); // Set active link on initial load

    // Add click event for navigation links to ensure smooth scroll and active state
    navLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const targetElement = document.querySelector(targetId);

            if (targetElement) {
                const headerOffset = header.offsetHeight;
                const elementPosition = targetElement.getBoundingClientRect().top + window.pageYOffset;
                const offsetPosition = elementPosition - headerOffset;

                window.scrollTo({
                    top: offsetPosition,
                    behavior: 'smooth'
                });

                navLinks.forEach(nav => nav.classList.remove('active'));
                this.classList.add('active');

                if (window.innerWidth <= 768) {
                    nav.classList.remove("show");
                }
            }
        });
    });


    // ========== Slideshow ==========
    let slideIndex = 0;
    let slideTimer = null;
    const slides = document.querySelectorAll(".mySlide");
    const dots = document.querySelectorAll(".dot");

    // Only run slideshow logic if elements exist
    if (slides.length > 0 && dots.length > 0) {
        function showSlide(index) {
            slides.forEach((slide) => (slide.style.display = "none"));
            dots.forEach((dot) => dot.classList.remove("active-dot"));

            slides[index].style.display = "block";
            dots[index].classList.add("active-dot");

            slideIndex = index;
        }

        function nextSlide() {
            slideIndex = (slideIndex + 1) % slides.length;
            showSlide(slideIndex);
        }

        function startSlideShow() {
            slideTimer = setInterval(nextSlide, 7000);
        }

        function resetSlideShow(index) {
            clearInterval(slideTimer);
            showSlide(index);
            startSlideShow();
        }
        
        dots.forEach((dot, idx) => {
            dot.addEventListener("click", () => resetSlideShow(idx));
        });

        showSlide(slideIndex);
        startSlideShow();
    }


    // ========== Language Dropdown ==========
    const langToggle = document.querySelector(".lang-toggle");
    const langMenu = document.querySelector(".lang-menu");
    const langDropdown = document.querySelector(".lang-dropdown");

    if (langToggle && langMenu && currentLangText && langDropdown) {
        langToggle.addEventListener("click", () => {
            langMenu.style.display =
                langMenu.style.display === "block" ? "none" : "block";
        });

        document.querySelectorAll(".lang-menu li").forEach((item) => {
            item.addEventListener("click", () => {
                const selectedLang = item.getAttribute("data-lang");
                currentLangText.textContent = selectedLang.toUpperCase();
                langMenu.style.display = "none";
            });
        });

        document.addEventListener("click", (e) => {
            if (!langDropdown.contains(e.target)) {
                langMenu.style.display = "none";
            }
        });
    }


    const scrollArrow = document.querySelector('.scroll-down-btn');

    if (scrollArrow) {
    scrollArrow.addEventListener('click', function (e) {
        e.preventDefault();

        const target = document.querySelector(this.getAttribute('href'));
        const headerOffset = document.querySelector('.header')?.offsetHeight || 0;

        if (target) {
        const targetPosition = target.getBoundingClientRect().top + window.pageYOffset;
        const scrollToPosition = targetPosition - headerOffset + 100;

        const start = window.scrollY;
        const distance = scrollToPosition - start;
        const duration = 1500;
        let startTime = null;

        function scrollStep(currentTime) {
            if (!startTime) startTime = currentTime;
            const timeElapsed = currentTime - startTime;
            const progress = Math.min(timeElapsed / duration, 1);
            const ease = 1 - Math.pow(1 - progress, 3);
            window.scrollTo(0, start + distance * ease);

            if (progress < 1) {
            requestAnimationFrame(scrollStep);
            }
        }

        requestAnimationFrame(scrollStep);
        }
    });
    }

    // ========== Story and Intern Video Popup ==========
    // The stories object is now handled dynamically inside updateDynamicContent
    const storyPopup = document.getElementById("storyPopup");
    const closeStory = document.getElementById("closeStory");
    const storyVideo = document.getElementById("storyVideo");
    const storyText = document.getElementById("storyText");
    const speedSelect = document.getElementById("speed");

    // Click listeners are added in updateDynamicContent
    if (closeStory) {
        closeStory.addEventListener("click", () => {
            storyPopup.style.display = "none";
            storyVideo.pause();
            storyVideo.currentTime = 0;
            storyVideo.src = "";
        });
    }
    
    if (speedSelect) {
        speedSelect.addEventListener("change", (e) => {
            storyVideo.playbackRate = parseFloat(e.target.value);
        });
    }

    if (storyPopup) {
        storyPopup.addEventListener('click', (e) => {
            if (e.target === storyPopup) {
                closeStory.click();
            }
        });
    }

    // ========== Service Card Expand ==========
    const serviceCards = document.querySelectorAll(".service-card");

    serviceCards.forEach((card) => {
        card.addEventListener("click", (e) => {
            if (e.target.closest("a, button, input, textarea, select")) return;

            const content = card.querySelector(".card-content");
            const toggleBtn = card.querySelector(".toggle-btn");
            const isOpen = content.classList.contains("open");

            if (isOpen) {
                content.classList.remove("open");
                toggleBtn.classList.remove("rotate");
                content.style.display = "none";
            } else {
                content.classList.add("open");
                toggleBtn.classList.add("rotate");
                content.style.display = "block";
            }
        });
    });


    // ========== CV Upload Functionality ==========
    const fileInput = document.getElementById("cv");
    const chooseFileBtn = document.getElementById("chooseFileBtn");
    const fileNameDisplay = document.getElementById("fileName");
    const removeFileBtn = document.getElementById("removeFileBtn");
    const form = document.querySelector(".cv-form");

    if (fileInput && chooseFileBtn && fileNameDisplay && removeFileBtn && form) {
        // When "Choose File" button is clicked
        chooseFileBtn.addEventListener("click", () => {
            fileInput.click();
        });

        // When a file is selected
        fileInput.addEventListener("change", () => {
            if (fileInput.files.length > 0) {
                fileNameDisplay.textContent = fileInput.files[0].name;
                removeFileBtn.style.display = "inline";
            } else {
                fileNameDisplay.textContent = "No file chosen";
                removeFileBtn.style.display = "none";
            }
        });

        // When remove icon is clicked
        removeFileBtn.addEventListener("click", () => {
            fileInput.value = "";
            fileNameDisplay.textContent = "No file chosen";
            removeFileBtn.style.display = "none";
        });

        // On form submit
        form.addEventListener("submit", function(e) {
            e.preventDefault();

            const formData = new FormData(form);

            fetch("submit_cv.php", {
                    method: "POST",
                    body: formData,
                })
                .then((response) => response.text())
                .then((result) => {
                    alert(result);
                    form.reset();
                    fileNameDisplay.textContent = "No file chosen";
                    removeFileBtn.style.display = "none";
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("An error occurred. Please try again.");
                });
        });
    }

    // ========== Event Tab Functionality ==========
    const tabs = document.querySelectorAll(".event-tab");
    const items = document.querySelectorAll(".event-item");
    const modal = document.getElementById("modal");
    const modalImg = document.getElementById("modalImg");
    const modalDesc = document.getElementById("modalDesc");
    const modalClose = document.getElementById("modalClose");
    const modalPrev = document.getElementById("modalPrev");
    const modalNext = document.getElementById("modalNext");

    let filteredItems = [];
    let currentIndex = 0;

    if (tabs.length && items.length && modal && modalImg && modalDesc && modalClose && modalPrev && modalNext) {
        // Filtering
        tabs.forEach((tab) => {
            tab.addEventListener("click", () => {
                tabs.forEach((btn) => btn.classList.remove("active"));
                tab.classList.add("active");

                const category = tab.getAttribute("data-category");

                filteredItems = [...items].filter((item) =>
                    item.getAttribute("data-category") === category
                );

                items.forEach((item) => {
                    item.style.display =
                        item.getAttribute("data-category") === category ? "block" : "none";
                });

                currentIndex = 0;
            });
        });

        window.addEventListener("DOMContentLoaded", () => {
            if (tabs.length > 0) {
                tabs[0].click();
            }
        });

        // Modal logic
        function openModal(index) {
            const item = filteredItems[index];
            if (item) {
                modalImg.src = item.querySelector("img").src;
                // Get the translated description from the data attribute
                modalDesc.textContent = item.getAttribute("data-description");
                modal.style.display = "flex";
                document.body.style.overflow = "hidden";
                currentIndex = index;
            }
        }

        function closeModal() {
            modal.style.display = "none";
            document.body.style.overflow = "";
        }

        function navigateModal(dir) {
            currentIndex += dir;
            if (currentIndex < 0) currentIndex = filteredItems.length - 1;
            if (currentIndex >= filteredItems.length) currentIndex = 0;
            openModal(currentIndex);
        }

        items.forEach((item) => {
            item.addEventListener("click", () => {
                const currentCategory = document.querySelector(".event-tab.active")?.getAttribute("data-category");
                filteredItems = [...items].filter(
                    (i) => i.getAttribute("data-category") === currentCategory
                );

                currentIndex = filteredItems.indexOf(item);
                openModal(currentIndex);
            });
        });

        modalClose.addEventListener("click", closeModal);
        modalPrev.addEventListener("click", () => navigateModal(-1));
        modalNext.addEventListener("click", () => navigateModal(1));
        modal.addEventListener("click", (e) => {
            if (e.target === modal) closeModal();
        });
    }

    // ========== Contact Form Submission ==========
    const contactForm = document.getElementById("contactForm");

    if (contactForm) {
        contactForm.addEventListener("submit", function (e) {
            e.preventDefault();

            const formData = new FormData(contactForm);

            fetch("submit_contact.php", {
                method: "POST",
                body: formData
            })
                .then((response) => response.text())
                .then((result) => {
                    alert(result);
                    contactForm.reset();
                    document.getElementById("otherCategoryInput").style.display = "none";
                })
                .catch((error) => {
                    console.error("Error:", error);
                    alert("Something went wrong. Please try again.");
                });
        });
    }


    const categorySelect = document.getElementById("category");
    const otherInput = document.getElementById("otherCategoryInput");

    categorySelect.addEventListener("change", function () {
        if (this.value === "other") {
            otherInput.style.display = "block";
        } else {
            otherInput.style.display = "none";
            otherInput.value = "";
        }
    });

    // Make sure to call updateDynamicContent on initial load as well
    // It's already called from inside loadLanguage, but this is good practice
    // to ensure all parts of the page are correctly initialized.
    loadLanguage(currentLang);
});