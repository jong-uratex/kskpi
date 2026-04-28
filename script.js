$(document).ready(function () {
    
    // --- 1. POP-UP (MODAL) LOGIC ---
    $('.view-details').on('click', function() {
        const btn = $(this);
        
        // Fill Header & Totals
        $('#m-name').text(btn.data('name'));
        $('#m-total').text(btn.data('total'));
        $('#m-admin-rem').text(btn.data('admin-rem') || "No remarks provided.");
        $('#m-emp-rem').text(btn.data('emp-rem') || "Waiting for employee response...");

        // Fill Score Table
        const tableBody = `
            <tr><td>Productivity</td><td>${btn.data('prod')}</td></tr>
            <tr><td>Quality of Work</td><td>${btn.data('qual')}</td></tr>
            <tr><td>Work Attitude</td><td>${btn.data('atti')}</td></tr>
            <tr><td>Teamwork</td><td>${btn.data('team')}</td></tr>
            <tr><td>Role-Specific KPI</td><td>${btn.data('kpi')}</td></tr>
        `;
        $('#m-score-table').html(tableBody);

        // Handle Signature Display
        if (btn.data('sig')) {
            $('#m-sig-img').attr('src', btn.data('sig'));
            $('#sig-section').show();
        } else {
            $('#sig-section').hide();
        }

        $('#detailsModal').modal('show');
    });

    // --- 2. SIGNATURE PAD LOGIC ---
    const canvas = document.getElementById("sig-canvas");
    if (canvas) {
        const ctx = canvas.getContext("2d");
        const sigData = document.getElementById("sig-data");
        let drawing = false;

        function getPos(e) {
            const rect = canvas.getBoundingClientRect();
            const clientX = e.touches ? e.touches[0].clientX : e.clientX;
            const clientY = e.touches ? e.touches[0].clientY : e.clientY;
            return { x: clientX - rect.left, y: clientY - rect.top };
        }

        function start(e) { 
            drawing = true; 
            ctx.beginPath(); 
            const p = getPos(e); 
            ctx.moveTo(p.x, p.y); 
            if(e.type === 'touchstart') e.preventDefault();
        }

        function move(e) {
            if (!drawing) return;
            const p = getPos(e);
            ctx.lineTo(p.x, p.y);
            ctx.stroke();
            if(e.type === 'touchmove') e.preventDefault();
        }

        canvas.addEventListener("mousedown", start);
        canvas.addEventListener("touchstart", start);
        canvas.addEventListener("mousemove", move);
        canvas.addEventListener("touchmove", move);
        window.addEventListener("mouseup", () => { 
            if(drawing) { drawing = false; sigData.value = canvas.toDataURL(); } 
        });
        canvas.addEventListener("touchend", () => { 
            if(drawing) { drawing = false; sigData.value = canvas.toDataURL(); } 
        });

        ctx.strokeStyle = "#000";
        ctx.lineWidth = 2;
    }
});

function clearSignature() {
    const canvas = document.getElementById("sig-canvas");
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    document.getElementById("sig-data").value = "";
}