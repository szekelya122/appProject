

    
    async function fetchNews() {
        const apiKey = 'Ocj3iA80IF4EIkYB0buZhnczGPqzjXxFWOPlVfT6_MONUTzJ'; 
        const url = `https://api.currentsapi.services/v1/latest-news?apiKey=${apiKey}`;
    
        try {
          // Fetch the data from the Currents API
          const response = await fetch(url);
          const data = await response.json();
    
          // Check if data is available
          if (data && data.news) {
            const container = document.getElementById('news-container');
            
            
            container.innerHTML = '';
    
            
            data.news.forEach(news => {
              const newsHTML = `
                <div style="margin-bottom: 20px;">
                  <h3><a href="${news.url}" target="_blank">${news.title}</a></h3>
                  <p>${news.description}</p>
                  <small>Source: ${news.source} | Published: ${news.published}</small>
                </div>
              `;
              container.innerHTML += newsHTML;
            });
          } else {
            console.log("No news found");
          }
        } catch (error) {
          console.error("Error fetching news:", error);
        }
      }
    
      fetchNews();
    