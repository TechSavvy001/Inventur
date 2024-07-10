document.getElementById('openCamera').addEventListener('click', function() {
    const video = document.createElement('video');
    const canvas = document.createElement('canvas');
    const context = canvas.getContext('2d');
    const captureButton = document.createElement('button');
    captureButton.textContent = 'Capture';

    document.body.appendChild(video);
    document.body.appendChild(captureButton);

    navigator.mediaDevices.getUserMedia({ video: true })
        .then((stream) => {
            video.srcObject = stream;
            video.play();
        })
        .catch((err) => {
            console.error('Error accessing the camera: ', err);
        });

    captureButton.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        context.drawImage(video, 0, 0, canvas.width, canvas.height);
        
        const imageUrl = canvas.toDataURL('image/png');
        document.getElementById('bildData').value = imageUrl;
        
        // Stop all video streams.
        video.srcObject.getTracks().forEach(track => track.stop());
        
        // Remove video and button elements from the DOM
        document.body.removeChild(video);
        document.body.removeChild(captureButton);
    });
});
