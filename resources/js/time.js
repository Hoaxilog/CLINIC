function updateLiveTime() {
        const timeElement = document.getElementById('realtime-time');
        const dateElement = document.getElementById('realtime-date');
        
        if (!timeElement || !dateElement) return; // Failsafe
        
        const now = new Date();
        
        // Format Time
        let hours = now.getHours();
        const minutes = now.getMinutes().toString().padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // 0 hour is 12
        const formattedTime = `${hours.toString().padStart(2, '0')}:${minutes} ${ampm}`;
        
        // Format Date
        const formattedDate = now.toLocaleString('en-US', {
        month: 'long',
        day: 'numeric',
        year: 'numeric'
        });
        
        // Update HTML
        if (timeElement.innerText !== formattedTime) {
        timeElement.innerText = formattedTime;
        }
        if (dateElement.innerText !== formattedDate) {
        dateElement.innerText = formattedDate;
        }
    }

    updateLiveTime();
    setInterval(updateLiveTime, 1000);