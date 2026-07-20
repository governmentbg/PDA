@props(['item', 'resource'])

<div class="container">
    <div class="row">
        <div id="viewer-container" class="col-lg-9">
            <div id="cultural-object-display" style="width: 100%; height: 600px; background: #000; position: relative;">
                <div id="osd-toolbar" aria-hidden="false" style="z-index: 10;"></div>
                <div id="loading-indicator" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); text-align: center; z-index: 5;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">{{ __('cultural_object.loading') }}</span>
                    </div>
                    <p class="mt-2">{{ __('cultural_object.loading_image') }}</p>
                </div>
            </div>
        </div>

        <div id="thumbnails-sidebar" class="col-lg-3 d-none" style="padding-left: 0;">
            <div class="sidebar-header"
                 style="background:#f8f9fa; padding:10px; border-bottom:1px solid #dee2e6; display:flex; align-items:center; justify-content:space-between;">

                <h6 class="mb-0">{{ __('pagination.pages') }}</h6>
            </div>
            <div id="thumbnails-container" style="height: 600px; overflow-y: auto; background: #fff; padding: 10px;">
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        @media (max-width: 991.98px) {
            #viewer-container {
                width: 100% !important;
                margin-left: 0 !important;
            }

            #thumbnails-sidebar {
                width: 100% !important;
                height: 150px !important;
                margin-top: 10px;
            }

            #thumbnails-container {
                height: 150px !important;
                overflow-x: auto;
                overflow-y: auto;
                white-space: nowrap;
                padding: 10px !important;
            }

            .thumbnail-item {
                display: inline-block;
                width: 100px;
                margin-right: 10px;
                margin-bottom: 0;
                vertical-align: top;
            }

            .thumbnail-img-container {
                height: 80px !important;
            }
        }

        .thumbnail-item {
            height: 160px;
            cursor: pointer;
            border: 2px solid transparent;
            transition: all 0.2s ease;
            margin-bottom: 10px;
            border-radius: 4px;
            overflow: hidden;
            background: #f8f9fa;
            display: block;
        }

        .thumbnail-item:hover {
            border-color: #6c757d;
            transform: translateY(-2px);
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }

        .thumbnail-item.active {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.05);
        }

        .thumbnail-img-container {
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            background: #e9ecef;
        }

        .thumbnail-img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
        }

        .thumbnail-label {
            font-size: 11px;
            text-align: center;
            padding: 5px;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            color: #495057;
        }

        .thumbnail-loading {
            width: 100%;
            height: 120px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e9ecef;
            color: #6c757d;
            font-size: 12px;
        }

        .thumbnail-placeholder {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 120px;
            background: #e9ecef;
            color: #6c757d;
            font-size: 24px;
        }

        .thumbnail-placeholder span {
            font-size: 12px;
            margin-top: 5px;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/openseadragon@5.0/build/openseadragon/openseadragon.min.js"></script>
    <link rel="stylesheet"
          href="https://cdn.jsdelivr.net/gh/Benomrans/openseadragon-icons@latest/openseadragon-icons.css"/>

    <script>
        (function() {
            if (window.culturalObjectViewerInitialized) {
                return;
            }
            window.culturalObjectViewerInitialized = true;

            $(document).ready(function () {
                const resource = @json($resource);
                const visualizationType = "{{ $resource?->visualizationtype }}";
                const isTiff = visualizationType === '{{ \App\Enums\CulturalObjectEnum::TIFF }}';
                const webId = "{{ $resource?->id }}";
                const THUMBNAIL_WIDTH = 150;
                const THUMBNAIL_HEIGHT = 120;
                const BATCH_SIZE = 20;
                const THRESHOLD_SCROLL = 0.8;

                if (!resource || !resource.web_resource_address) return;

                let allTileSources = [];
                let loadedThumbnailPages = 0;
                let isLoadingThumbnails = false;
                let isLoadingMore = false;
                let currentPage = 0;
                let isThumbnailsVisible = false;
                let viewerInitialized = false;
                let hasReachedEnd = false;

                const thumbnailsCache = {};

                function getPageUrl(pageNumber) {
                    return "{{ route('cultural_object.tiff-page', ['web_id' => ':web_id', 'page_number' => ':page_number']) }}"
                        .replace(':web_id', webId)
                        .replace(':page_number', pageNumber);
                }

                async function loadPage(pageNumber) {
                    try {
                        const response = await fetch(getPageUrl(pageNumber));
                        if (!response.ok) return null;
                        const data = await response.json();
                        return (data.error || data.success === false) ? null : data;
                    } catch (err) {
                        console.error(`Error loading page ${pageNumber}:`, err);
                        return null;
                    }
                }

                function generateThumbnailUrl(tileSource, pageNumber) {
                    if (!tileSource || !tileSource['@id']) return null;
                    const iiifBaseUrl = tileSource['@id'];
                    const thumbnailUrl = `${iiifBaseUrl}/full/!${THUMBNAIL_WIDTH},${THUMBNAIL_HEIGHT}/0/default.jpg`;
                    return thumbnailUrl;
                }

                function createThumbnailElement(pageNumber, thumbnailUrl = null) {
                    const div = document.createElement('div');
                    div.className = 'thumbnail-item';
                    div.dataset.page = pageNumber;
                    div.style.cursor = 'pointer';

                    const imgContainer = document.createElement('div');
                    imgContainer.className = 'thumbnail-img-container';
                    div.appendChild(imgContainer);

                    const label = document.createElement('div');
                    label.className = 'thumbnail-label';
                    label.textContent = "{{ __('pagination.page') }} " + pageNumber;
                    div.appendChild(label);

                    if (thumbnailUrl) {
                        const img = new Image();
                        img.src = thumbnailUrl;
                        img.className = 'thumbnail-img';
                        img.alt = `Page ${pageNumber}`;
                        img.crossOrigin = 'anonymous';

                        img.onload = function() {
                            thumbnailsCache[pageNumber] = thumbnailUrl;
                            const loadingDiv = imgContainer.querySelector('.thumbnail-loading');
                            if (loadingDiv) loadingDiv.remove();
                        };

                        img.onerror = function() {
                            imgContainer.innerHTML = `<div class="thumbnail-placeholder"><span>${pageNumber}</span></div>`;
                        };

                        imgContainer.innerHTML = `
                    <div class="thumbnail-loading">
                    <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
                    </div>`;
                        imgContainer.appendChild(img);
                    } else {
                        imgContainer.innerHTML = `<div class="thumbnail-placeholder"><span>${pageNumber}</span></div>`;
                    }

                    div.addEventListener('click', function() {
                        if (window.viewer) goToPage(parseInt(this.dataset.page));
                    });

                    return div;
                }

                function renderPlaceholders() {
                    const container = document.getElementById('thumbnails-container');
                    container.innerHTML = '';
                    for (let i = 1; i <= allTileSources.length; i++) {
                        const element = createThumbnailElement(i, thumbnailsCache[i] || null);
                        container.appendChild(element);
                    }
                    loadedThumbnailPages = 0;
                    toggleThumbnailsSidebar(allTileSources.length > 1);
                    loadThumbnailsBatch();
                    setupThumbnailsScrollListener();
                }

                async function loadThumbnailsBatch() {
                    if (isLoadingThumbnails || loadedThumbnailPages >= allTileSources.length) return;

                    const container = document.getElementById('thumbnails-container');
                    if (!container) return;

                    isLoadingThumbnails = true;
                    const startPage = loadedThumbnailPages + 1;
                    const endPage = Math.min(startPage + BATCH_SIZE - 1, allTileSources.length);

                    for (let pageNumber = startPage; pageNumber <= endPage; pageNumber++) {
                        const tileSource = allTileSources[pageNumber - 1];
                        const thumbnailUrl = generateThumbnailUrl(tileSource, pageNumber);
                        const newThumb = createThumbnailElement(pageNumber, thumbnailUrl);
                        const oldThumb = container.querySelector(`.thumbnail-item[data-page="${pageNumber}"]`);
                        if (oldThumb) container.replaceChild(newThumb, oldThumb);
                    }

                    loadedThumbnailPages = endPage;
                    isLoadingThumbnails = false;
                }

                function checkIfNeedMorePages() {
                    if (hasReachedEnd || isLoadingMore || isLoadingThumbnails) {
                        return false;
                    }

                    if (currentPage >= allTileSources.length - 3) {
                        loadMorePagesIfNeeded();
                        return true;
                    }
                    return false;
                }

                function checkScrollLoad() {
                    const container = document.getElementById('thumbnails-container');
                    if (!container || allTileSources.length === 0) return;

                    const scrollPosition = container.scrollTop + container.clientHeight;
                    const scrollHeight = container.scrollHeight;
                    const scrollPercentage = scrollPosition / scrollHeight;

                    if (scrollPercentage > THRESHOLD_SCROLL &&
                        loadedThumbnailPages < allTileSources.length &&
                        !isLoadingThumbnails) {
                        loadThumbnailsBatch();
                        return;
                    }

                    if (scrollPercentage > 0.9 &&
                        loadedThumbnailPages >= allTileSources.length &&
                        !hasReachedEnd &&
                        !isLoadingMore &&
                        !isLoadingThumbnails) {
                        loadMorePagesIfNeeded();
                    }
                }

                function setupThumbnailsScrollListener() {
                    const container = document.getElementById('thumbnails-container');
                    if (!container) return;

                    let scrollTimeout;
                    container.removeEventListener('scroll', checkScrollLoad);

                    container.addEventListener('scroll', function() {
                        clearTimeout(scrollTimeout);
                        scrollTimeout = setTimeout(checkScrollLoad, 50);
                    });
                }

                function toggleThumbnailsSidebar(show) {
                    const sidebar = document.getElementById('thumbnails-sidebar');
                    const viewerContainer = document.getElementById('viewer-container');
                    const closeBtn = document.getElementById('thumbnails-close-btn');
                    const toggleBtn = document.getElementById('thumbnails-toggle-btn');

                    if (show && allTileSources.length > 1) {
                        sidebar.classList.remove('d-none');
                        sidebar.classList.add('d-block');
                        viewerContainer.classList.remove('col-lg-12');
                        viewerContainer.classList.add('col-lg-9');
                        isThumbnailsVisible = true;
                        if (closeBtn) closeBtn.style.display = 'block';
                        if (toggleBtn) toggleBtn.style.display = 'none';
                    } else {
                        sidebar.classList.add('d-none');
                        sidebar.classList.remove('d-block');
                        viewerContainer.classList.remove('col-lg-9');
                        viewerContainer.classList.add('col-lg-12');
                        isThumbnailsVisible = false;
                        if (closeBtn) closeBtn.style.display = 'none';
                        if (toggleBtn) toggleBtn.style.display = allTileSources.length > 1 ? 'block' : 'none';
                    }
                }

                function updateActiveThumbnail(pageNumber) {
                    const container = document.getElementById('thumbnails-container');

                    document.querySelectorAll('.thumbnail-item').forEach(item => {
                        const itemPage = parseInt(item.dataset.page);

                        if (itemPage === pageNumber) {
                            item.classList.add('active');

                            setTimeout(() => {
                                if (!container) return;

                                const containerRect = container.getBoundingClientRect();
                                const itemRect = item.getBoundingClientRect();

                                const offset =
                                    itemRect.top -
                                    containerRect.top +
                                    container.scrollTop -
                                    (container.clientHeight / 2) +
                                    (item.clientHeight / 2);

                                container.scrollTo({
                                    top: offset,
                                    behavior: 'auto'
                                });

                            }, 100);
                        } else {
                            item.classList.remove('active');
                        }
                    });
                }

                function goToPage(pageNumber) {
                    if (window.viewer && pageNumber >= 1 && pageNumber <= allTileSources.length) {
                        window.viewer.goToPage(pageNumber - 1);
                        currentPage = pageNumber - 1;
                        updateActiveThumbnail(pageNumber);
                        checkIfNeedMorePages();
                    }
                }

                async function loadMorePagesIfNeeded() {
                    if (isLoadingMore || hasReachedEnd) {
                        return;
                    }

                    isLoadingMore = true;
                    const currentTotal = allTileSources.length;
                    const pagesToLoad = 10;
                    const nextStartPage = currentTotal + 1;

                    try {
                        const promises = [];
                        for (let i = nextStartPage; i < nextStartPage + pagesToLoad; i++) {
                            promises.push(loadPage(i));
                        }

                        const newTileSources = await Promise.all(promises);
                        const validTileSources = newTileSources.filter(p => p !== null);

                        if (validTileSources.length === 0) {
                            hasReachedEnd = true;
                            isLoadingMore = false;
                            return;
                        }

                        if (validTileSources.length > 0) {
                            const prevLength = allTileSources.length;
                            allTileSources.push(...validTileSources);
                            const container = document.getElementById('thumbnails-container');
                            for (let i = prevLength + 1; i <= allTileSources.length; i++) {
                                const existingThumb = container.querySelector(`.thumbnail-item[data-page="${i}"]`);
                                if (!existingThumb) {
                                    const placeholder = createThumbnailElement(i, null);
                                    container.appendChild(placeholder);
                                }
                            }

                            if (window.viewer) {
                                const currentPageBeforeUpdate = currentPage;
                                window.viewer.open(allTileSources, currentPageBeforeUpdate);
                            }

                            if (loadedThumbnailPages >= prevLength) {
                                loadThumbnailsBatch();
                            }
                        }
                    } catch (err) {
                        console.error('Error loading more pages:', err);
                    } finally {
                        isLoadingMore = false;
                    }
                }

                function initializeViewer(tileSources) {
                    const hasMultiple = tileSources.length > 1;

                    if (window.viewer && viewerInitialized) {
                        window.viewer.destroy();
                        viewerInitialized = false;
                    }
                    if (tileSources.length === 0) return;

                    window.viewer = OpenSeadragon({
                        id: "cultural-object-display",
                        prefixUrl: "https://cdn.jsdelivr.net/gh/Benomrans/openseadragon-icons@latest/images/",
                        tileSources: hasMultiple ? tileSources : tileSources[0],
                        sequenceMode: hasMultiple,
                        showReferenceStrip: false,
                        showSequenceControl: hasMultiple,
                        showNavigationControl: true,
                        showZoomControl: true,
                        showHomeControl: true,
                        showFullPageControl: true,
                        showRotationControl: true,
                        showNavigator: false,
                        constrainDuringPan: true,
                        crossOriginPolicy: 'Anonymous',
                        ajaxWithCredentials: false,
                        timeout: 10000,
                    });
                    viewerInitialized = true;

                    window.viewer.addHandler('open', hideLoading);


                    window.viewer.addHandler('page', function(event) {
                        currentPage = event.page;
                        updateActiveThumbnail(currentPage + 1);
                    });

                    if (hasMultiple) {
                        renderPlaceholders();
                    } else {
                        toggleThumbnailsSidebar(false);
                    }

                    return window.viewer;
                }

                function showLoading() { $('#loading-indicator').show(); }
                function hideLoading() { $('#loading-indicator').hide(); }

                function setupUIControls() {
                    if (!document.querySelector('link[href*="font-awesome"]')) {
                        const fa = document.createElement('link');
                        fa.rel = 'stylesheet';
                        fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css';
                        document.head.appendChild(fa);
                    }

                    const toggleBtn = document.createElement('button');
                    toggleBtn.id = 'thumbnails-toggle-btn';
                    toggleBtn.className = 'btn btn-sm btn-outline-secondary';
                    toggleBtn.innerHTML = '<i class="fas fa-th"></i>';
                    toggleBtn.style.position = 'absolute';
                    toggleBtn.style.top = '10px';
                    toggleBtn.style.right = '10px';
                    toggleBtn.style.zIndex = '1000';
                    toggleBtn.title = 'Toggle Thumbnails';
                    toggleBtn.addEventListener('click', () => toggleThumbnailsSidebar(!isThumbnailsVisible && allTileSources.length > 1));
                    document.getElementById('cultural-object-display').appendChild(toggleBtn);

                    const closeBtn = document.createElement('button');
                    closeBtn.id = 'thumbnails-close-btn';
                    closeBtn.className = 'btn btn-sm btn-outline-secondary';
                    closeBtn.innerHTML = '<i class="fas fa-times"></i>';
                    closeBtn.style.zIndex = '1000';
                    closeBtn.title = 'Close Thumbnails';
                    closeBtn.style.display = 'none';

                    closeBtn.addEventListener('click', () => toggleThumbnailsSidebar(false));

                    document.querySelector('.sidebar-header').appendChild(closeBtn);
                }

                showLoading();
                setupUIControls();

                if (isTiff) {
                    const initialPages = Array.from({length: 10}, (_, i) => i + 1);
                    Promise.all(initialPages.map(loadPage))
                        .then(results => {
                            allTileSources = results.filter(p => p !== null);
                            initializeViewer(allTileSources);
                        })
                        .catch(err => { console.error(err); hideLoading(); });
                } else {
                    allTileSources = [{ type: 'image', url: resource.web_resource_address }];
                    initializeViewer(allTileSources);
                }
            });
        })();
    </script>

{{--    --}}{{-- Download multiple files --}}
{{--    <script>--}}
{{--        document.addEventListener('DOMContentLoaded', () => {--}}
{{--            const btn = document.getElementById('download-all');--}}
{{--            if (!btn) return;--}}

{{--            const urls = @json(--}}
{{--                    $item?->has_web_view_resource?->map(fn($r)--}}
{{--                    => route('cultural_object.download', ['id' => $item->id, 'res' => $r->id]))->values()--}}
{{--                  );--}}
{{--            if (!Array.isArray(urls) || urls.length === 0) return;--}}

{{--            btn.addEventListener('click', () => {--}}
{{--                btn.disabled = true;--}}

{{--                const STAGGER_MS = 400;--}}
{{--                const TTL_MS = 60000;--}}

{{--                urls.forEach((url, i) => {--}}
{{--                    setTimeout(() => {--}}
{{--                        const iframe = document.createElement('iframe');--}}
{{--                        iframe.hidden = true;--}}
{{--                        iframe.src = url;--}}
{{--                        document.body.appendChild(iframe);--}}

{{--                        // remove iframe--}}
{{--                        setTimeout(() => {--}}
{{--                            try {--}}
{{--                                document.body.removeChild(iframe);--}}
{{--                            } catch (e) {}--}}
{{--                        }, TTL_MS);--}}

{{--                        // re-enable btn--}}
{{--                        if (i === urls.length - 1) {--}}
{{--                            setTimeout(() => {--}}
{{--                                btn.disabled = false;--}}
{{--                            }, 1500);--}}
{{--                        }--}}
{{--                    }, i * STAGGER_MS);--}}
{{--                });--}}
{{--            });--}}
{{--        });--}}
{{--    </script>--}}
@endpush
