document.addEventListener("DOMContentLoaded", function () {
    console.log("news.js loaded! Fetching news...");

    const apiKey = "b33aa4c207ca6158d1cea6b242fd879f6023ad338a672ce8159efb9d14b67517"; 
    const url = `https://serpapi.com/search?engine=google_news&api_key=${apiKey}`;

    fetch(url,{mode:"no-cors"})
        .then(response => response.json())
        .then(data => {
            console.log("Full API Response:", data); 

            
            if (!data.news_results || data.news_results.length === 0) {
                console.warn("No news found.");
                document.getElementById("news-container").innerHTML = "<p>Nincs elérhető hír.</p>";
                return;
            }

            console.log("News found, updating UI...");
            const newsContainer = document.getElementById("news-container");
            newsContainer.innerHTML = ""; 

            data.news_results.slice(0, 5).forEach(article => {
                console.log(`Adding article: ${article.title}`); 

                const newsItem = `
                    <div class="card bg-dark text-white mb-3">
                        <div class="card-body">
                            <h5 class="card-title">${article.title}</h5>
                            
                            <a href="${article.link}" target="_blank" class="text-gold">Read more</a>
                        </div>
                    </div>
                `;

                newsContainer.insertAdjacentHTML("beforeend", newsItem);
            });
        })
});
