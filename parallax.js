const visual = document.getElementById('visual');
const back = document.querySelector('.curve-back');
const front = document.querySelector('.curve-front');
const fg = document.querySelector('.visual-foreground');

const clamp = (v, min, max) => Math.min(Math.max(v, min), max);

let targetX = 0, targetY = 0;
let cx = 0, cy = 0;

document.addEventListener('mousemove', (e) => {
    const rect = visual.getBoundingClientRect();
    const centerX = rect.left + rect.width / 2;
    const centerY = rect.top + rect.height / 2;

    targetX = clamp((e.clientX - centerX) / rect.width * 40, -25, 25);
    targetY = clamp((e.clientY - centerY) / rect.height * 40, -25, 25);
});

function animate() {
    cx += (targetX - cx) * 0.08;
    cy += (targetY - cy) * 0.08;
    back.style.transform  = `translate(${cx * 0.5}px, ${cy * 0.5}px)`;
    front.style.transform = `translate(${cx}px, ${cy}px)`;
    fg.style.transform    = `translate(${cx * 1.8}px, ${cy * 1.8}px)`;

    requestAnimationFrame(animate);
}

animate();
