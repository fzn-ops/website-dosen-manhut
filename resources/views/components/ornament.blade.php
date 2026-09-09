<div class="fixed top-0 left-0 w-screen h-screen pointer-events-none -z-50 overflow-hidden" style="opacity: 0.15;" id="ornament-container">
    <svg id="pattern-svg" class="w-full h-full" xmlns="http://www.w3.org/2000/svg"></svg>
</div>

<script>
    document.addEventListener("DOMContentLoaded", () => {
        const svg = document.getElementById('pattern-svg');
        if (!svg) return;
        
        const colors = ['#1a3675', '#eab308', '#9ca3af']; 
        
        const petalSize = 11; 
        const spacing = 25; 

        function drawPattern() {
            svg.innerHTML = ''; 
            
            const width = window.innerWidth;
            const height = window.innerHeight;
            
            const cols = Math.ceil(width / spacing) + 1;
            const rows = Math.ceil(height / spacing) + 1;

            // 1. Radius dikurangi menjadi 50% (sebelumnya 0.55) agar tidak terlalu ke tengah
            const radius = Math.min(width, height) * 0.50;

            for (let i = 0; i < cols; i++) {
                for (let j = 0; j < rows; j++) {
                    
                    const x = i * spacing;
                    const y = j * spacing;
                    
                    const distKiriAtas = Math.sqrt(x * x + y * y);
                    const distKananBawah = Math.sqrt((width - x) * (width - x) + (height - y) * (height - y));

                    let density = 0;

                    // 2. Pangkat dinaikkan menjadi 2 (sebelumnya 1.5) agar jumlah daun lebih cepat rontok/berkurang saat menjauh dari sudut
                    if (distKiriAtas < radius) {
                        density = Math.pow(1 - (distKiriAtas / radius), 2);
                    } else if (distKananBawah < radius) {
                        density = Math.pow(1 - (distKananBawah / radius), 2);
                    } else {
                        continue; 
                    }

                    if (density > 0.05) {
                        const angles = [0, 90, 180, 270];
                        
                        angles.forEach(angle => {
                            // 3. Pengali 1.5 dihilangkan. Probabilitas murni dari density.
                            // Hasilnya: Sudut tetap rapi, tapi makin ke tengah makin banyak bolongnya.
                            if (Math.random() < density) {
                                const color = colors[Math.floor(Math.random() * colors.length)];
                                
                                const g = document.createElementNS('http://www.w3.org/2000/svg', 'g');
                                g.setAttribute('transform', `translate(${x}, ${y}) rotate(${angle})`);
                                
                                const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                                path.setAttribute('d', `M 0 0 Q 0 -${petalSize} ${petalSize} -${petalSize} Q ${petalSize} 0 0 0`);
                                path.setAttribute('fill', color);
                                
                                g.appendChild(path);
                                svg.appendChild(g);
                            }
                        });
                    }
                }
            }
        }

        drawPattern();
        
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(drawPattern, 250);
        });
    });
</script>