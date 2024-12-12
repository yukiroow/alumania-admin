const allTabs = document.getElementById("ul-posts").children;
let sortOrder = 1;

function setActiveTab(index) {
  const tab = allTabs.item(index);
  document.getElementById("searchContainer").innerHTML = ``;
  sortOrder = 1;
  for (let i = 0; i < 3; i++) {
    const item = allTabs.item(i);
    item.classList.remove("selected-tab");
    item.children.item(1).style.color = "rgba(0, 0, 0, 0.4)";
    switch (i) {
      case 0:
        item.children.item(0).src = "../../res/experience-posts.png";
        break;
      case 1:
        item.children.item(0).src = "../../res/calendar-posts.png";
        break;
      case 2:
        item.children.item(0).src = "../../res/jlisting-posts.png";
        break;
    }
  }

  tab.classList.add("selected-tab");
  tab.children.item(1).style.color = "white";
  switch (index) {
    case 0:
      tab.children.item(0).src = "../../res/experience-posts-white.png";
      break;
    case 1:
      document.getElementById("searchContainer").innerHTML = `
                <div class="event-search-bar">
                <input type="text" class="event-search-input" placeholder="Search" id="event-search-text">
                <button class="event-search-button" onclick="
                        currentEvents = searchEvent(currentEvents);
                        displayEvents(currentEvents);
                ">
                    <img src="../../res/search.png" alt="Search">
                </button>
                </div>

                <div class="event-category-dropdown">
                    <button class="event-category-button" onclick="eventCategory()">
                        Category
                        <img src="../../res/arrow.png" alt="Dropdown Arrow" class="event-dropdown-arrow">
                    </button>
                    <div class="event-dropdown-content" id="categoryDropdown">
                        <button onclick="
                        currentEvents = filterEvent(events, 'Seminar')
                        displayEvents(currentEvents);
                        ">Seminar</button>
                        <button onclick="
                        currentEvents = filterEvent(events, 'Thanksgiving')
                        displayEvents(currentEvents);
                        ">Thanksgiving</button>
                        <button onclick="
                        currentEvents = filterEvent(events, 'Festival')
                        displayEvents(currentEvents);
                        ">Festival</button>
                        <button onclick="
                        currentEvents = filterEvent(events, 'Reunion')
                        displayEvents(currentEvents);
                        ">Reunion</button>
                    </div>
                </div>
                <div class="sort-dropdown">
                    <button class="sort-button" onclick="
                        sortCategory();
                    ">
                        <img src="../../res/sort.png" alt="Sort">
                    </button>
                    <div class="sort-content" id="sortDropdown">
                        <button onclick="
                        sortCategory();
                        sortEventsAsc(currentEvents);
                        displayEvents(currentEvents);
                        " class="sort-item">Ascending</button>
                        <button onclick="
                        sortCategory();
                        sortEventsDesc(currentEvents);
                        displayEvents(currentEvents);
                        " class="sort-item">Descending</button>
                    </div>
                </div>
            `;
      tab.children.item(0).src = "../../res/calendar-posts-white.png";
      break;
    case 2:
      document.getElementById("searchContainer").innerHTML = `
                <div class="job-search-bar">
                <input type="text" class="job-search-input" placeholder="Search" id="job-search-text">
                <button class="job-search-button" onclick="
                    currentJobs = searchJobs(jobs);
                    displayJobs(currentJobs);
                ">
                    <img src="../../res/search.png" alt="Search">
                </button>
                </div>
                <div class="sort-dropdown">
                    <button class="sort-button" onclick="
                        sortCategory();
                    ">
                        <img src="../../res/sort.png" alt="Sort">
                    </button>
                    <div class="sort-content" id="sortDropdown">
                        <button onclick="
                        sortCategory();
                        sortJobsAsc(currentJobs);
                        displayJobs(currentJobs);
                        " class="sort-item">Ascending</button>
                        <button onclick="
                        sortCategory();
                        sortJobsDesc(currentJobs);
                        displayJobs(currentJobs);
                        " class="sort-item">Descending</button>
                    </div>
                </div>
            `;
      tab.children.item(0).src = "../../res/jlisting-posts-white.png";
      break;
  }
}

function filterEvent(events, category) {
  function checkCategory(event) {
    return event.category.toLowerCase() == category.toLowerCase();
  }
  return events.filter(checkCategory);
}

function searchEvent(events) {
  query = document.getElementById("event-search-text").value;
  function searchEvent(event) {
    return `${event.title} ${event.description} ${event.eventloc}`
      .toLowerCase()
      .includes(query.toLowerCase());
  }

  return events.filter(searchEvent);
}

function searchJobs(jobs) {
  query = document.getElementById("job-search-text").value;
  function searchJob(job) {
    return `${job.title}${job.description}${job.location}${job.companyname}`
      .toLowerCase()
      .includes(query.toLowerCase());
  }
  return jobs.filter(searchJob);
}

function sortEventsAsc(events) {
  events.sort(
    (a, b) =>
      new Date(`${a.eventdate}T${a.eventtime}`) -
      new Date(`${b.eventdate}T${b.eventtime}`)
  );
}

function sortEventsDesc(events) {
  events.sort(
    (a, b) =>
      new Date(`${b.eventdate}T${b.eventtime}`) -
      new Date(`${a.eventdate}T${a.eventtime}`)
  );
}

function sortJobsAsc(jobs) {
  jobs.sort(
    (a, b) =>
      new Date(a.publishtimestamp.replace(" ", "T")) -
      new Date(b.publishtimestamp.replace(" ", "T"))
  );
}

function sortJobsDesc(jobs) {
  jobs.sort(
    (a, b) =>
      new Date(b.publishtimestamp.replace(" ", "T")) -
      new Date(a.publishtimestamp.replace(" ", "T"))
  );
}

function eventCategory() {
  document.getElementById("categoryDropdown").classList.toggle("show");
}

function sortCategory() {
  document.getElementById("sortDropdown").classList.toggle("show");
}
