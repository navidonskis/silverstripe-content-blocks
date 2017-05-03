export function createRandomId() {
    return (0 | Math.random() * 9e6).toString(36);
}